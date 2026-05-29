-- SARPAMS - Stray Animals Rescue & Pet Adoption Management System
-- Complete MySQL Database Schema with Sample Data

CREATE DATABASE IF NOT EXISTS sarpams CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sarpams;

-- ============================================================
-- CORE TABLES
-- ============================================================

CREATE TABLE IF NOT EXISTS SHELTER (
    shelter_id    INT AUTO_INCREMENT PRIMARY KEY,
    shelter_name  VARCHAR(100) NOT NULL UNIQUE,
    address       VARCHAR(200) NOT NULL,
    city          VARCHAR(60)  NOT NULL,
    capacity      INT          NOT NULL,
    contact_phone VARCHAR(15)  NOT NULL,
    manager_name  VARCHAR(100) NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS CAGE (
    cage_id       INT AUTO_INCREMENT PRIMARY KEY,
    shelter_id    INT NOT NULL,
    cage_number   VARCHAR(10) NOT NULL,
    size_category ENUM('Small','Medium','Large') NOT NULL,
    is_occupied   BOOLEAN DEFAULT FALSE,
    notes         TEXT,
    FOREIGN KEY (shelter_id) REFERENCES SHELTER(shelter_id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS ANIMAL (
    animal_id     INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(60) NOT NULL,
    species       VARCHAR(40) NOT NULL,
    breed         VARCHAR(60),
    age_years     DECIMAL(4,1),
    sex           CHAR(1) CHECK (sex IN ('M','F','U')),
    colour        VARCHAR(60) NOT NULL,
    weight_kg     DECIMAL(5,2),
    microchip_no  VARCHAR(25) UNIQUE,
    intake_date   DATE NOT NULL,
    health_status VARCHAR(20) DEFAULT 'Unknown',
    is_vaccinated BOOLEAN DEFAULT FALSE,
    is_neutered   BOOLEAN DEFAULT FALSE,
    cage_id       INT,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cage_id) REFERENCES CAGE(cage_id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS RESCUER (
    rescuer_id          INT AUTO_INCREMENT PRIMARY KEY,
    first_name          VARCHAR(50) NOT NULL,
    last_name           VARCHAR(50) NOT NULL,
    phone               VARCHAR(15) NOT NULL UNIQUE,
    email               VARCHAR(100) UNIQUE,
    zone_area           VARCHAR(60) NOT NULL,
    certification_level ENUM('Basic','Intermediate','Advanced') NOT NULL,
    is_available        BOOLEAN DEFAULT TRUE,
    join_date           DATE NOT NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS RESCUE_REQUEST (
    request_id        INT AUTO_INCREMENT PRIMARY KEY,
    report_date       DATE NOT NULL,
    report_time       TIME NOT NULL,
    location_address  VARCHAR(200) NOT NULL,
    latitude          DECIMAL(9,6),
    longitude         DECIMAL(9,6),
    status            ENUM('Open','Assigned','Closed') NOT NULL DEFAULT 'Open',
    citizen_name      VARCHAR(100) NOT NULL,
    citizen_phone     VARCHAR(15) NOT NULL,
    rescuer_id        INT,
    animal_id         INT,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rescuer_id) REFERENCES RESCUER(rescuer_id) ON DELETE SET NULL,
    FOREIGN KEY (animal_id)  REFERENCES ANIMAL(animal_id)  ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS VETERINARIAN (
    vet_id          INT AUTO_INCREMENT PRIMARY KEY,
    first_name      VARCHAR(50) NOT NULL,
    last_name       VARCHAR(50) NOT NULL,
    specialisation  VARCHAR(100),
    phone           VARCHAR(15) NOT NULL,
    email           VARCHAR(100) UNIQUE,
    license_no      VARCHAR(30) NOT NULL UNIQUE,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS MEDICAL_RECORD (
    record_id         INT AUTO_INCREMENT PRIMARY KEY,
    animal_id         INT NOT NULL,
    vet_id            INT NOT NULL,
    exam_date         DATE NOT NULL,
    diagnosis         TEXT,
    treatment         TEXT,
    medication        VARCHAR(200),
    next_checkup_date DATE,
    notes             TEXT,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (animal_id) REFERENCES ANIMAL(animal_id) ON DELETE CASCADE,
    FOREIGN KEY (vet_id)    REFERENCES VETERINARIAN(vet_id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS FOSTER_FAMILY (
    foster_id      INT AUTO_INCREMENT PRIMARY KEY,
    family_name    VARCHAR(100) NOT NULL,
    address        VARCHAR(200) NOT NULL,
    city           VARCHAR(60)  NOT NULL,
    phone          VARCHAR(15)  NOT NULL,
    email          VARCHAR(100),
    house_type     VARCHAR(60),
    has_other_pets BOOLEAN DEFAULT FALSE,
    is_approved    BOOLEAN DEFAULT FALSE,
    approval_date  DATE,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS FOSTER_PLACEMENT (
    placement_id  INT AUTO_INCREMENT PRIMARY KEY,
    animal_id     INT NOT NULL,
    foster_id     INT NOT NULL,
    start_date    DATE NOT NULL,
    expected_end  DATE,
    actual_end    DATE,
    outcome       VARCHAR(100),
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (animal_id) REFERENCES ANIMAL(animal_id) ON DELETE CASCADE,
    FOREIGN KEY (foster_id) REFERENCES FOSTER_FAMILY(foster_id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS ADOPTION_APPLICANT (
    applicant_id       INT AUTO_INCREMENT PRIMARY KEY,
    first_name         VARCHAR(50)  NOT NULL,
    last_name          VARCHAR(50)  NOT NULL,
    dob                DATE,
    address            VARCHAR(200) NOT NULL,
    city               VARCHAR(60)  NOT NULL,
    phone              VARCHAR(15)  NOT NULL,
    email              VARCHAR(100),
    occupation         VARCHAR(100),
    has_previous_pets  BOOLEAN DEFAULT FALSE,
    living_situation   VARCHAR(100),
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS ADOPTION (
    adoption_id      INT AUTO_INCREMENT PRIMARY KEY,
    animal_id        INT NOT NULL,
    applicant_id     INT NOT NULL,
    officer_id       INT NOT NULL,
    application_date DATE NOT NULL,
    approval_date    DATE,
    adoption_date    DATE,
    status           ENUM('Pending','Approved','Rejected','Completed') NOT NULL DEFAULT 'Pending',
    agreement_signed BOOLEAN DEFAULT FALSE,
    notes            TEXT,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (animal_id)    REFERENCES ANIMAL(animal_id)              ON DELETE RESTRICT,
    FOREIGN KEY (applicant_id) REFERENCES ADOPTION_APPLICANT(applicant_id) ON DELETE RESTRICT,
    FOREIGN KEY (officer_id)   REFERENCES RESCUER(rescuer_id)            ON DELETE RESTRICT
);

-- ============================================================
-- INDEXES
-- ============================================================
CREATE INDEX idx_animal_species  ON ANIMAL(species);
CREATE INDEX idx_animal_health   ON ANIMAL(health_status);
CREATE INDEX idx_animal_intake   ON ANIMAL(intake_date);
CREATE INDEX idx_rescue_status   ON RESCUE_REQUEST(status);
CREATE INDEX idx_adoption_status ON ADOPTION(status);
CREATE INDEX idx_medrecord_animal ON MEDICAL_RECORD(animal_id);
CREATE INDEX idx_cage_shelter    ON CAGE(shelter_id);

-- ============================================================
-- TRIGGERS
-- ============================================================

DELIMITER $$

CREATE TRIGGER trg_adoption_complete
AFTER UPDATE ON ADOPTION FOR EACH ROW
BEGIN
    IF NEW.status = 'Completed' AND OLD.status != 'Completed' THEN
        UPDATE ANIMAL SET health_status = 'Adopted' WHERE animal_id = NEW.animal_id;
    END IF;
END$$

CREATE TRIGGER trg_prevent_double_adopt
BEFORE INSERT ON ADOPTION FOR EACH ROW
BEGIN
    DECLARE cnt INT;
    SELECT COUNT(*) INTO cnt FROM ADOPTION
    WHERE animal_id = NEW.animal_id AND status IN ('Approved','Completed');
    IF cnt > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Animal already adopted or pending approval.';
    END IF;
END$$

CREATE TRIGGER trg_cage_occupy_on_assign
AFTER UPDATE ON ANIMAL FOR EACH ROW
BEGIN
    IF NEW.cage_id IS NOT NULL AND (OLD.cage_id IS NULL OR OLD.cage_id != NEW.cage_id) THEN
        UPDATE CAGE SET is_occupied = TRUE WHERE cage_id = NEW.cage_id;
        IF OLD.cage_id IS NOT NULL THEN
            UPDATE CAGE SET is_occupied = FALSE WHERE cage_id = OLD.cage_id;
        END IF;
    END IF;
    IF NEW.cage_id IS NULL AND OLD.cage_id IS NOT NULL THEN
        UPDATE CAGE SET is_occupied = FALSE WHERE cage_id = OLD.cage_id;
    END IF;
END$$

DELIMITER ;

-- ============================================================
-- VIEWS
-- ============================================================

CREATE OR REPLACE VIEW vw_available_animals AS
    SELECT a.animal_id, a.name, a.species, a.breed, a.age_years,
           a.sex, a.colour, a.health_status, a.is_vaccinated,
           s.shelter_name, s.city
    FROM ANIMAL a
    LEFT JOIN CAGE c    ON a.cage_id    = c.cage_id
    LEFT JOIN SHELTER s ON c.shelter_id = s.shelter_id
    WHERE a.health_status NOT IN ('Adopted')
      AND a.animal_id NOT IN (
          SELECT animal_id FROM ADOPTION WHERE status IN ('Approved','Completed')
      );

CREATE OR REPLACE VIEW vw_shelter_occupancy AS
    SELECT s.shelter_id, s.shelter_name, s.city, s.capacity,
           COUNT(c.cage_id)     AS total_cages,
           SUM(c.is_occupied)   AS occupied_cages,
           s.capacity - SUM(IFNULL(c.is_occupied,0)) AS free_cages,
           ROUND(SUM(IFNULL(c.is_occupied,0))*100.0/NULLIF(COUNT(c.cage_id),0),1) AS occupancy_pct
    FROM SHELTER s
    LEFT JOIN CAGE c ON s.shelter_id = c.shelter_id
    GROUP BY s.shelter_id;

CREATE OR REPLACE VIEW vw_adoption_pipeline AS
    SELECT ad.adoption_id,
           CONCAT(ap.first_name,' ',ap.last_name) AS applicant_name,
           a.name    AS animal_name,
           a.species AS animal_species,
           ad.application_date, ad.approval_date,
           ad.adoption_date, ad.status, ad.agreement_signed
    FROM ADOPTION ad
    JOIN ANIMAL              a  ON ad.animal_id    = a.animal_id
    JOIN ADOPTION_APPLICANT  ap ON ad.applicant_id = ap.applicant_id
    ORDER BY ad.application_date DESC;

-- ============================================================
-- SAMPLE DATA
-- ============================================================

INSERT INTO SHELTER (shelter_name, address, city, capacity, contact_phone, manager_name) VALUES
('Happy Paws Shelter',  '12 MG Road, Bengaluru',       'Bengaluru', 80, '080-12345678', 'Ravi Kumar'),
('City Animal Care',    '45 Ring Road, Mumbai',         'Mumbai',    60, '022-87654321', 'Priya Nair'),
('Green Earth Rescue',  '78 NH-48, Mysuru',             'Mysuru',    45, '0821-9876543', 'Suresh Rao'),
('Pawsitive Shelter',   '23 Brigade Road, Bengaluru',   'Bengaluru', 50, '080-55667788', 'Anita Sharma');

INSERT INTO CAGE (shelter_id, cage_number, size_category, is_occupied, notes) VALUES
(1,'C-01','Small',  FALSE, 'Clean, disinfected'),
(1,'C-02','Small',  FALSE, NULL),
(1,'C-03','Medium', FALSE, 'New paint'),
(1,'C-04','Medium', TRUE,  NULL),
(1,'C-05','Large',  TRUE,  'Holds two animals'),
(2,'A-01','Small',  FALSE, NULL),
(2,'A-02','Medium', FALSE, NULL),
(2,'A-03','Large',  TRUE,  NULL),
(3,'B-01','Small',  TRUE,  NULL),
(3,'B-02','Medium', FALSE, NULL);

INSERT INTO ANIMAL (name, species, breed, age_years, sex, colour, weight_kg, microchip_no, intake_date, health_status, is_vaccinated, is_neutered, cage_id) VALUES
('Bruno',    'Dog', 'Labrador Mix',      3.0, 'M', 'Golden Brown',  28.5, 'MC001234', '2024-01-15', 'Healthy',         TRUE,  TRUE,  4),
('Whiskers', 'Cat', 'Indian Shorthair',  1.5, 'F', 'White & Grey',   3.8, 'MC001235', '2024-02-10', 'Under Treatment', FALSE, FALSE, NULL),
('Max',      'Dog', 'German Shepherd',   5.0, 'M', 'Black & Tan',   32.0, 'MC001236', '2024-03-01', 'Healthy',         TRUE,  FALSE, 5),
('Mango',    'Cat', 'Indian Shorthair',  1.0, 'F', 'Orange & White', 3.2, 'MC001237', '2024-03-15', 'Healthy',         TRUE,  TRUE,  8),
('Tiger',    'Dog', 'Indie Mix',         2.5, 'M', 'Brown & White', 18.0, 'MC001238', '2024-04-01', 'Healthy',         TRUE,  FALSE, 9),
('Luna',     'Cat', 'Persian Mix',       3.5, 'F', 'White',          4.5, NULL,        '2024-04-20', 'Healthy',         TRUE,  TRUE,  NULL),
('Rocky',    'Dog', 'Rottweiler Mix',    4.0, 'M', 'Black',         35.0, 'MC001240', '2024-05-01', 'Under Treatment', FALSE, FALSE, NULL),
('Bella',    'Dog', 'Beagle Mix',        2.0, 'F', 'Tri-colour',    10.5, 'MC001241', '2024-05-10', 'Healthy',         TRUE,  TRUE,  NULL);

INSERT INTO RESCUER (first_name, last_name, phone, email, zone_area, certification_level, is_available, join_date) VALUES
('Arjun',   'Mehta',   '9876543210', 'arjun@sarpams.org',   'North Bengaluru',  'Advanced',     TRUE,  '2022-01-15'),
('Sneha',   'Patil',   '9876543211', 'sneha@sarpams.org',   'South Bengaluru',  'Intermediate', TRUE,  '2022-06-01'),
('Kiran',   'Reddy',   '9876543212', 'kiran@sarpams.org',   'East Bengaluru',   'Basic',        FALSE, '2023-03-10'),
('Pooja',   'Verma',   '9876543213', 'pooja@sarpams.org',   'West Bengaluru',   'Intermediate', TRUE,  '2023-07-20'),
('Rahul',   'Singh',   '9876543214', 'rahul@sarpams.org',   'Central Mumbai',   'Advanced',     TRUE,  '2021-11-05'),
('Divya',   'Nair',    '9876543215', 'divya@sarpams.org',   'South Mumbai',     'Basic',        TRUE,  '2024-01-08');

INSERT INTO RESCUE_REQUEST (report_date, report_time, location_address, latitude, longitude, status, citizen_name, citizen_phone, rescuer_id, animal_id) VALUES
('2024-01-14','09:30:00','Near KR Market, Bengaluru',        12.9716, 77.5946, 'Closed',   'Ramesh Kumar',    '9900123456', 1, 1),
('2024-02-09','14:20:00','Indiranagar Signal, Bengaluru',     12.9784, 77.6408, 'Closed',   'Kavitha Rao',     '9900123457', 2, 2),
('2024-03-01','08:00:00','Koramangala Park, Bengaluru',       12.9352, 77.6245, 'Closed',   'Suresh Babu',     '9900123458', 1, 3),
('2024-05-15','11:45:00','Brigade Road Junction, Bengaluru',  12.9716, 77.6078, 'Open',     'Anand Sharma',    '9900123459', NULL, NULL),
('2024-05-20','16:30:00','Near Mysore Palace, Mysuru',        12.3052, 76.6552, 'Assigned', 'Lakshmi Devi',    '9900123460', 5, NULL),
('2024-05-25','07:15:00','Andheri West, Mumbai',              19.1197, 72.8468, 'Open',     'Pradeep Joshi',   '9900123461', NULL, NULL);

INSERT INTO VETERINARIAN (first_name, last_name, specialisation, phone, email, license_no) VALUES
('Dr. Anil',    'Sharma',   'Small Animals',   '9811234567', 'anil.vet@clinic.com',   'VET-KA-001'),
('Dr. Meena',   'Iyer',     'Surgery & Ortho', '9811234568', 'meena.vet@clinic.com',  'VET-KA-002'),
('Dr. Rajan',   'Pillai',   'Dermatology',     '9811234569', 'rajan.vet@clinic.com',  'VET-MH-001'),
('Dr. Preethi', 'Gowda',    'General Practice','9811234570', 'preethi.vet@clinic.com','VET-KA-003');

INSERT INTO MEDICAL_RECORD (animal_id, vet_id, exam_date, diagnosis, treatment, medication, next_checkup_date, notes) VALUES
(1, 1, '2024-01-16', 'Healthy – routine check',         'Deworming administered',  'Albendazole 400mg',    '2024-07-16', 'Annual booster due in July'),
(2, 2, '2024-02-11', 'Mild respiratory infection',      'Antibiotics prescribed',  'Amoxicillin 50mg x7d','2024-03-11', 'Monitor breathing'),
(2, 1, '2024-03-12', 'Recovering well',                  'Continued antibiotics',  'Amoxicillin 25mg x5d','2024-04-12', 'Full recovery expected'),
(3, 1, '2024-03-02', 'Hip dysplasia – mild',            'Pain management',          'Meloxicam 1mg/kg',    '2024-06-02', 'Limit strenuous activity'),
(5, 4, '2024-04-02', 'Minor skin rash on abdomen',      'Topical antifungal',       'Clotrimazole cream',  '2024-05-02', 'Keep dry and clean'),
(7, 3, '2024-05-03', 'Severe mange and malnutrition',   'Intensive care protocol', 'Ivermectin + vitamins','2024-06-03', 'Isolate from other animals');

INSERT INTO FOSTER_FAMILY (family_name, address, city, phone, email, house_type, has_other_pets, is_approved, approval_date) VALUES
('Sharma Family',  '14 Jayanagar, Bengaluru',    'Bengaluru', '9922334455', 'sharma.family@gmail.com', 'Independent House', TRUE,  TRUE,  '2023-12-01'),
('Patel Household','22 Bandra West, Mumbai',      'Mumbai',    '9922334456', 'patel.home@gmail.com',    'Apartment',         FALSE, TRUE,  '2024-01-10'),
('Reddy Family',   '55 Madhapur, Hyderabad',      'Hyderabad', '9922334457', 'reddy.family@gmail.com',  'Independent House', TRUE,  FALSE, NULL),
('Kumar Home',     '8 Rajajinagar, Bengaluru',    'Bengaluru', '9922334458', 'kumar.home@gmail.com',    'Apartment',         FALSE, TRUE,  '2024-02-15');

INSERT INTO FOSTER_PLACEMENT (animal_id, foster_id, start_date, expected_end, actual_end, outcome) VALUES
(6, 1, '2024-05-01', '2024-07-01', NULL,         'Ongoing'),
(8, 4, '2024-05-15', '2024-07-15', NULL,         'Ongoing');

INSERT INTO ADOPTION_APPLICANT (first_name, last_name, dob, address, city, phone, email, occupation, has_previous_pets, living_situation) VALUES
('Arun',     'Sharma',   '1990-05-20', '45 Koramangala, Bengaluru', 'Bengaluru', '9876543210', 'arun@email.com',    'Software Engineer', TRUE,  'Independent House'),
('Meena',    'Pillai',   '1985-08-15', '12 Bandra East, Mumbai',    'Mumbai',    '9876543211', 'meena@email.com',   'Teacher',           FALSE, 'Apartment'),
('Vikram',   'Nair',     '1992-11-30', '78 Whitefield, Bengaluru',  'Bengaluru', '9876543212', 'vikram@email.com',  'Doctor',            TRUE,  'Independent House'),
('Ananya',   'Rao',      '1995-03-10', '23 Juhu, Mumbai',           'Mumbai',    '9876543213', 'ananya@email.com',  'Architect',         FALSE, 'Apartment'),
('Suresh',   'Babu',     '1988-07-22', '90 JP Nagar, Bengaluru',    'Bengaluru', '9876543214', 'suresh@email.com',  'Business Owner',    TRUE,  'Independent House');

INSERT INTO ADOPTION (animal_id, applicant_id, officer_id, application_date, approval_date, adoption_date, status, agreement_signed, notes) VALUES
(1, 1, 2, '2024-04-01', '2024-04-10', '2024-04-15', 'Completed', TRUE,  'Happy adoption – regular follow-up scheduled'),
(4, 2, 4, '2024-05-01', '2024-05-08', NULL,          'Approved',  TRUE,  'Adoption date pending'),
(3, 3, 1, '2024-05-10', NULL,          NULL,          'Pending',   FALSE, 'Background check in progress'),
(5, 4, 2, '2024-05-20', NULL,          NULL,          'Pending',   FALSE, 'Home visit scheduled');
