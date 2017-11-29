DROP  DATABASE IF EXISTS nma;
CREATE DATABASE nma;
USE nma;

create table person (
	id							int									not null auto_increment,
    ssn						int									not null,
    firstName				varchar(25)						not null,
    lastName				varchar(25)						not null,
    gender					enum('n/a', 'm','f')			not null,
    dob						date									not null,
    phnumb				char(11)							not null,
    
	primary key(id)
);

create table address (
	id							int						not null auto_increment,
    address				varchar(50)			not null,
    address2				varchar(50),
    city						varchar(50)			not null,
    state					char(3)					not null,
    zipcode				char(10)				not null,
    
    primary key(id)
);

create table person_address (
	person					int		not null,
    address				int		not null,
    
    primary key(person, address),
    foreign key(person) references person(id),
    foreign key(address) references address(id)
);

-- Staff Section 

create table staff_type(
	id							int						not null auto_increment,
    `name`					char(15)				not null,
    primary key(id)
);

create table staff (
	id					int			not null,
    `type`			int			not null,
    
    foreign key(id) references person(id),
    foreign key(`type`) references staff_type(id)
);

create table salary (
	id							int						not null,
    amount				double(8,2)			not null,
    
    foreign key(id)	references staff(id)
);

create table contract (
	id							int						not null,
    rate						double(8,2)			not null,
    duration				int						not null,
    
    
   foreign key(id)	references staff(id)
);

create table shift(
	id							int						not null,
    `code`					char(5)					not null,
    
    primary key(id)
);

create table staff_shift(
	staff				int				not null,
    shift				int				not null,
    
    foreign key(staff) references staff(id),
    foreign key(shift)  references shift(id)
);


create table skill (
	id							int							not null,
    `name`					char(15)					not null,
    
    primary key(id)
);

create table staff_skill (
	staff				int				not null,
    skill				int				not null,
    
    foreign key(staff) references staff(id),
    foreign key(skill)  references skill(id)
);



-- Patient Section
create table patient (
	id									int					not null,
	pnum							int					not null											auto_increment,
    blood_type					enum('o+', 'o-', 'a+', 'a-', 'b+', 'b-', 'ab+', 'ab-')			not null,
    admit_date					date					not null,
    cholesterol					char(10)			not null,
    blood_sugar				int(4)				not null,
    
    
    primary key (pnum),
    foreign key(id) references person(id)
);

create table appointment_type(
	id									int						not null		auto_increment,
    `name`							varchar(50)			not null,
    description					text						not null,
    
    primary key(id)
);

create table appointment_type_skill (
	`type`			int			not null,
    skill				int			not null,
    
    foreign key(`type`) references appointment_type(id),
    foreign key(skill) references skill(id)
);

create table appointment(
	id									int						not null		auto_increment,
    `type`							int						not null,
    
    primary key(id),
    foreign key(`type`)	references appointment_type(id)
);

create table surgery_type (
	id									int						not null	auto_increment,
    `name`							varchar(50)			not null,
    description					text						null,
    category						varchar(25)			not null,
    
    primary key(id)
);

create table surgery (
	id									int						not null	auto_increment,
    `type`							int						not null,
    
    primary key(id),
    foreign key(`type`) references surgery_type(id)
);

create table patient_appointment(
	appt								int						not null,
    patient							int						not null,
    
    foreign key(appt)	references appointment(id),
    foreign key(patient) references patient(id)
);

create table staff_appointment(
	appt								int						not null,
    staff								int						not null,
    
    foreign key(appt)	references appointment(id),
    foreign key(staff) references staff(id)
);

create table history(
	id									int						not null	auto_increment,
    patient							int						not null,
    appt								int						null,
    
    primary key(id),
    foreign key(patient) references patient(id),
    foreign key(appt) references appointment(id)
);


create table medication (
	`code`							int						not null		auto_increment,
    cost								decimal(9,2)		not null,
    qty								int						not null,
    backordered				int						not null,
    
    primary key(`code`)
);
create table condition_type (
	id									int						not null	auto_increment,
    `name`							varchar(10)			not null,
    
    primary key(id)
);

create table `condition` (
	id									int						not null	auto_increment,
    `name`							varchar(50)			not null,
    description					text						not null,
    `type`							int 						not null,
    
	primary key(id),
    foreign key(`type`) references condition_type(id)
);

create table patient_condition(
	patient							int						not null,
    `condition`					int						not null,
    
    foreign key(patient)	references patient(id),
    foreign key(`condition`) references `condition`(id)
);

create table prescription (
	medication					int						not null,
    patient							int						not null,
    `condition`					int						not null,
    staff								int						not null,
    
    foreign key(medication) references medication(`code`),
    foreign key(patient) references patient(id),
    foreign key(`condition`) references `condition`(id),
    foreign key(staff) references staff(id)
);

