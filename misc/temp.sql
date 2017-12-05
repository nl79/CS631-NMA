DROP DATABASE IF EXISTS nma;
CREATE DATABASE nma;
USE nma;

CREATE TABLE person (
  id        INT                    NOT NULL AUTO_INCREMENT,
  ssn       INT                    NOT NULL,
  firstName VARCHAR(25)            NOT NULL,
  lastName  VARCHAR(25)            NOT NULL,
  gender    ENUM ('n/a', 'm', 'f') NOT NULL,
  dob       DATE                   NOT NULL,
  phnumb    CHAR(11)               NOT NULL,

  PRIMARY KEY (id)
);

CREATE TABLE address (
  id       INT         NOT NULL AUTO_INCREMENT,
  address  VARCHAR(50) NOT NULL,
  address2 VARCHAR(50),
  city     VARCHAR(50) NOT NULL,
  state    CHAR(3)     NOT NULL,
  zipcode  CHAR(10)    NOT NULL,

  PRIMARY KEY (id)
);

CREATE TABLE person_address (
  person  INT NOT NULL,
  address INT NOT NULL,

  PRIMARY KEY (person, address),
  FOREIGN KEY (person) REFERENCES person (id),
  FOREIGN KEY (address) REFERENCES address (id)
);

-- Staff Section 

CREATE TABLE staff_type (
  id     INT      NOT NULL AUTO_INCREMENT,
  `name` CHAR(15) NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE staff (
  id           INT                                                    NOT NULL,
  snum         INT                                                    NOT NULL  AUTO_INCREMENT,
  `role`       ENUM ('nurse', 'surgeon', 'physician', 'cardiologist') NOT NULL,
  `type`       ENUM ('salary', 'contract')                            NOT NULL,
  compensation DOUBLE(9, 2)                                           NOT NULL  DEFAULT 0.00,
  start_date   DATE                                                   NOT NULL,
  duration     DOUBLE                                                 NOT NULL  DEFAULT 0.00,
  `status`     ENUM ('active', 'inactive')                            NOT NULL  DEFAULT 'active',

  PRIMARY KEY (snum),
  FOREIGN KEY (id) REFERENCES person (id)
  -- foreign key(`type`) references staff_type(id)
);

CREATE TABLE salary (
  id     INT          NOT NULL,
  amount DOUBLE(8, 2) NOT NULL,

  FOREIGN KEY (id) REFERENCES staff (id)
);

CREATE TABLE contract (
  id       INT          NOT NULL,
  rate     DOUBLE(8, 2) NOT NULL,
  duration INT          NOT NULL,


  FOREIGN KEY (id) REFERENCES staff (id)
);

CREATE TABLE shift (
  id     INT     NOT NULL,
  `code` CHAR(5) NOT NULL,

  PRIMARY KEY (id)
);

CREATE TABLE staff_shift (
  staff INT NOT NULL,
  shift INT NOT NULL,

  FOREIGN KEY (staff) REFERENCES staff (id),
  FOREIGN KEY (shift) REFERENCES shift (id)
);


CREATE TABLE skill (
  id     INT      NOT NULL,
  `name` CHAR(15) NOT NULL,

  PRIMARY KEY (id)
);

CREATE TABLE staff_skill (
  staff INT NOT NULL,
  skill INT NOT NULL,

  FOREIGN KEY (staff) REFERENCES staff (id),
  FOREIGN KEY (skill) REFERENCES skill (id)
);

-- Patient Section
CREATE TABLE patient (
  id          INT                                                     NOT NULL,
  pnum        INT                                                     NOT NULL                      AUTO_INCREMENT,
  blood_type  ENUM ('o+', 'o-', 'a+', 'a-', 'b+', 'b-', 'ab+', 'ab-') NOT NULL,
  admit_date  DATE                                                    NOT NULL,
  cholesterol CHAR(10)                                                NOT NULL,
  blood_sugar INT(4)                                                  NOT NULL,
  `primary`   INT                                                     NULL,

  PRIMARY KEY (pnum),
  FOREIGN KEY (id) REFERENCES person (id),
  FOREIGN KEY (`primary`) REFERENCES staff (id)
);

CREATE TABLE patient_staff (
  patient     INT NOT NULL,
  staff 		INT NOT NULL,

  FOREIGN KEY (patient) REFERENCES patient (id),
  FOREIGN KEY (staff) REFERENCES staff (id)
);

CREATE TABLE appointment_type (
  id          INT         NOT NULL    AUTO_INCREMENT,
  `name`      VARCHAR(50) NOT NULL,
  description TEXT        NOT NULL,

  PRIMARY KEY (id)
);

CREATE TABLE appointment_type_skill (
  `type` INT NOT NULL,
  skill  INT NOT NULL,

  FOREIGN KEY (`type`) REFERENCES appointment_type (id),
  FOREIGN KEY (skill) REFERENCES skill (id)
);

CREATE TABLE appointment (
  id     INT NOT NULL    AUTO_INCREMENT,
  `type` INT NOT NULL,

  PRIMARY KEY (id),
  FOREIGN KEY (`type`) REFERENCES appointment_type (id)
);

CREATE TABLE surgery_type (
  id          INT         NOT NULL  AUTO_INCREMENT,
  `name`      VARCHAR(50) NOT NULL,
  description TEXT        NULL,
  category    VARCHAR(25) NOT NULL,

  PRIMARY KEY (id)
);

CREATE TABLE surgery (
  id     INT NOT NULL  AUTO_INCREMENT,
  `type` INT NOT NULL,

  PRIMARY KEY (id),
  FOREIGN KEY (`type`) REFERENCES surgery_type (id)
);

CREATE TABLE patient_appointment (
  appt    INT NOT NULL,
  patient INT NOT NULL,

  FOREIGN KEY (appt) REFERENCES appointment (id),
  FOREIGN KEY (patient) REFERENCES patient (id)
);

CREATE TABLE staff_appointment (
  appt  INT NOT NULL,
  staff INT NOT NULL,

  FOREIGN KEY (appt) REFERENCES appointment (id),
  FOREIGN KEY (staff) REFERENCES staff (id)
);

CREATE TABLE history (
  id      INT NOT NULL  AUTO_INCREMENT,
  patient INT NOT NULL,
  appt    INT NULL,

  PRIMARY KEY (id),
  FOREIGN KEY (patient) REFERENCES patient (id),
  FOREIGN KEY (appt) REFERENCES appointment (id)
);


CREATE TABLE medication (
  `code`      INT           NOT NULL    AUTO_INCREMENT,
  cost        DECIMAL(9, 2) NOT NULL,
  qty         INT           NOT NULL,
  backordered INT           NOT NULL,

  PRIMARY KEY (`code`)
);
CREATE TABLE condition_type (
  id     INT         NOT NULL  AUTO_INCREMENT,
  `name` VARCHAR(10) NOT NULL,

  PRIMARY KEY (id)
);

CREATE TABLE `condition` (
  id          INT         NOT NULL  AUTO_INCREMENT,
  `name`      VARCHAR(50) NOT NULL,
  description TEXT        NOT NULL,
  `type`      INT         NOT NULL,

  PRIMARY KEY (id),
  FOREIGN KEY (`type`) REFERENCES condition_type (id)
);

CREATE TABLE patient_condition (
  patient     INT NOT NULL,
  `condition` INT NOT NULL,

  FOREIGN KEY (patient) REFERENCES patient (id),
  FOREIGN KEY (`condition`) REFERENCES `condition` (id)
);

CREATE TABLE prescription (
  medication  INT NOT NULL,
  patient     INT NOT NULL,
  `condition` INT NOT NULL,
  staff       INT NOT NULL,

  FOREIGN KEY (medication) REFERENCES medication (`code`),
  FOREIGN KEY (patient) REFERENCES patient (id),
  FOREIGN KEY (`condition`) REFERENCES `condition` (id),
  FOREIGN KEY (staff) REFERENCES staff (id)
);

-- Default Data
INSERT INTO condition_type (`name`)
VALUES ('allergy'), ('disease'), ('illness');

