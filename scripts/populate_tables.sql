-- populate_tables.sql

INSERT INTO region(region_id, country, province, city) VALUES (1, 'Canada', 'Quebec', 'Montreal');
INSERT INTO region(region_id, country, province, city) VALUES (2, 'Canada', 'Ontario', 'Toronto');
INSERT INTO region(region_id, country, province, city) VALUES (3, 'Canada', 'Quebec', 'Laval');
INSERT INTO region(region_id, country, province, city) VALUES (4, '日本', '関東', '東京');
INSERT INTO interests(interest_name) VALUES ('Fishing');
INSERT INTO interests(interest_name) VALUES ('Soccer');
INSERT INTO interests(interest_name) VALUES ('Basketball');
INSERT INTO interests(interest_name) VALUES ('Aliens');
INSERT INTO interests(interest_name) VALUES ('Fantasy Books');
INSERT INTO profession(profession_name) VALUES ('software developer');
INSERT INTO profession(profession_name) VALUES ('student');
INSERT INTO member(member_id, username, password,
                   first_name, last_name,
                   user_email, date_of_birth, is_admin,
                   status, region_access, lives_in, professions_access,
                   interests_access,
                   dob_access, email_access)
VALUES (1, 'johnsmith',
  '$2y$10$3r.tgTgusETeYuKstAbqb.AooeLLU9RFhUJTIKXDW5HJk3Hjyft8K',
  'John',
  'Smith',
  'johnsmith@warmup.project.ca',
  '1990-06-12', 'N', 'A', -1,3, -1, -1, -1, -1);

INSERT INTO member(member_id, username, password,
                   first_name, last_name,
                   user_email, date_of_birth, is_admin,
                   status, region_access, lives_in, professions_access,
                   interests_access,
                   dob_access, email_access)
VALUES (2, 'ndalo',
           '$2y$10$gZc2loyYSeJuB48JILeSeuGgG1038zzE3VhvH.j7ybOSidpiT4yNu',
           'Ndalo',
           'Zolani',
           'ndalo.zolani@warmup.project.ca',
           '1989-12-13', 'N', 'A', -1,3, -1, -1, -1, -1);

INSERT INTO member(member_id, username, password,
first_name, last_name,
user_email, date_of_birth, is_admin,
status, region_access, lives_in, professions_access,
interests_access,
dob_access, email_access)
VALUES (3, 'haruhisuzumiya',
'$2y$10$ail5Y3rzubZCSH1yDeqHo.VhWW3ce9plNM59Gkw.5pbk5DF899mk2',
'ハルヒ', '涼宮',
'suzumiya.haruhi@warmup.project.ca',
'1992-07-26', 'Y', 'A', -1,4, -1, -1, -1, -1);

INSERT INTO member(member_id, username, password,
                   first_name, last_name,
                   user_email, date_of_birth, is_admin,
                   status, region_access, lives_in, professions_access,
                   interests_access,
                   dob_access, email_access)
VALUES (4, 'robertom',
           '$2y$10$D6F1JbRmdGr0coOVIMcj1.ySlMdNuISj3P3FzupHqFdbTp0BAGbVS',
           'Roberto',
           'McDonald',
           'roberto.m@warmup.project.ca',
           '1959-04-08', 'N', 'I', -1,1, -1, -1, -1, -1);

INSERT INTO member(member_id, username, password,
                   first_name, last_name,
                   user_email, date_of_birth, is_admin,
                   status, region_access, lives_in, professions_access,
                   interests_access,
                   dob_access, email_access)
VALUES (5, 'rohit',
           '$2y$10$e1b7JEyG4L0vU9lJPI.r8uFjlgmbt7asRcaW4YiJHb0HShZGxVwai',
           'Rohit',
           'Singh',
           'rohit.singh@warmup.project.ca',
           '1994-10-17', 'N', 'S', -1,2, -1, -1, -1, -1);

INSERT INTO member(member_id, username, password,
                   first_name, last_name,
                   user_email, date_of_birth, is_admin,
                   status, region_access, lives_in, professions_access,
                   interests_access,
                   dob_access, email_access)
VALUES (6, 'admin',
           '$2y$10$swN87lyk6IeGJ6uJgeqRRusDkxpFJI9BkJimRfZWVmMMIoOzpWOku',
           'Admin',
           'Admin',
           'admin@powon.ca',
           '1999-12-31', 'Y', 'A', 0,NULL, 0, 0, 0, 0);

INSERT INTO works_as(member_id, profession_name) VALUES (3, 'student');
INSERT INTO works_as(member_id, profession_name) VALUES (4, 'software developer');
INSERT INTO has_interests(interest_name, member_id) VALUES ('Fishing', 1);
INSERT INTO has_interests(interest_name, member_id) VALUES ('Fishing', 2);
INSERT INTO has_interests(interest_name, member_id) VALUES ('Basketball', 2);
INSERT INTO has_interests(interest_name, member_id) VALUES ('Aliens', 3);
INSERT INTO has_interests(interest_name, member_id) VALUES ('Fantasy Books', 4);
INSERT INTO has_interests(interest_name, member_id) VALUES ('Soccer', 4);

-- Rohit has not selected his interests yet.

INSERT INTO related_members(member_from, member_to, relation_type, approval_date)
    VALUES
      (1, 3, 'F', CURRENT_TIMESTAMP);

INSERT INTO powon_group(powon_group_id, group_title, description, group_owner)
VALUES
  (-1, 'Admin group', 'Dedicated group for POWON administrators', 6);

INSERT INTO powon_group(powon_group_id, group_title, description, group_owner)
VALUES
  (1, 'Lord of the Rings Fans', 'A relaxed group to share information about The Lord Of The Rings.', 3);

INSERT INTO powon_group(powon_group_id, group_title, description, group_owner)
VALUES
  (2, 'Project R', 'A mysterious group working on the so-called ''Project R''', 4);

INSERT INTO powon_group(powon_group_id, group_title, description, group_owner)
VALUES
  (3, 'POWON work group', 'A group to discuss POWON implementation details. Add a page for each dedicated task', 5);

INSERT INTO `is_group_member`(powon_group_id, member_id, approval_date) VALUES (-1, 6, CURRENT_TIMESTAMP);
INSERT INTO `is_group_member`(powon_group_id, member_id, approval_date) VALUES (-1, 3, CURRENT_TIMESTAMP);

INSERT INTO `is_group_member`(powon_group_id, member_id, approval_date) VALUES (1, 4, CURRENT_TIMESTAMP);
INSERT INTO `is_group_member`(powon_group_id, member_id, approval_date) VALUES (2, 4, CURRENT_TIMESTAMP);
INSERT INTO `is_group_member`(powon_group_id, member_id, approval_date) VALUES (2, 5, CURRENT_TIMESTAMP);
INSERT INTO `is_group_member`(powon_group_id, member_id, approval_date) VALUES (3, 5, CURRENT_TIMESTAMP);
INSERT INTO `is_group_member`(powon_group_id, member_id, approval_date) VALUES (3, 1, CURRENT_TIMESTAMP);

INSERT INTO `invoice` (amount_due, payment_deadline, billing_period_start,
                       billing_period_end, account_holder)
VALUES (32.00, '2016-07-12 00:00:00', CURRENT_TIMESTAMP, '2017-06-12 00:00:00', 4);

INSERT INTO `invoice` (amount_due, payment_deadline, billing_period_start,
                       billing_period_end, account_holder)
VALUES (32.00, '2016-07-12 00:00:00', CURRENT_TIMESTAMP, '2017-06-12 00:00:00', 2);

INSERT INTO `invoice` (amount_due, payment_deadline, billing_period_start,
                       billing_period_end, account_holder)
VALUES (32.00, '2016-07-12 00:00:00', CURRENT_TIMESTAMP, '2017-06-12 00:00:00', 1);

-- page for public posts.

INSERT INTO `page`(page_id, page_title, date_created) VALUES (-1, 'Public posts', CURRENT_TIMESTAMP);
INSERT INTO `group_page` (page_id, page_description, access_type, page_owner, page_group) VALUES
  (-1, 'Post the public posts on this page', 'E', 6, -1);

INSERT INTO `post`(post_date_created, post_type, path_to_resource, post_body, comment_permission, parent_post, page_id, author_id) VALUES
  (CURRENT_TIMESTAMP, 'T', NULL, 'Welcome to POWON!
Please register a user account and feel free to use this very powerful system.
After registering, you may complete your profile, search and join groups,
create your own groups and add friends to see their posts.', 'V', NULL, -1, 6);

INSERT INTO `gift_inventory`(gift_name) VALUES ('Candy');
INSERT INTO `gift_inventory`(gift_name) VALUES ('Snorkel');
INSERT INTO `gift_inventory`(gift_name) VALUES ('Surfboard');
INSERT INTO `gift_inventory`(gift_name) VALUES ('Ball');
INSERT INTO `gift_inventory`(gift_name) VALUES ('Cool Outfit');
INSERT INTO `gift_inventory`(gift_name) VALUES ('Water Bottle');
INSERT INTO `gift_inventory`(gift_name) VALUES ('Agenda');
INSERT INTO `gift_inventory`(gift_name) VALUES ('Strawberries');
INSERT INTO `gift_inventory`(gift_name) VALUES ('Mandolin');
INSERT INTO `gift_inventory`(gift_name) VALUES ('Mew pokemon');
INSERT INTO `gift_inventory`(gift_name) VALUES ('Mystery Novel');

INSERT INTO `wish_list`(gift_name, member_id) VALUES ('Candy', 1);
INSERT INTO `wish_list`(gift_name, member_id) VALUES ('Snorkel', 2);
INSERT INTO `wish_list`(gift_name, member_id) VALUES ('Surfboard', 3);
INSERT INTO `wish_list`(gift_name, member_id) VALUES ('Ball', 4);
INSERT INTO `wish_list`(gift_name, member_id) VALUES ('Cool Outfit', 5);
INSERT INTO `wish_list`(gift_name, member_id) VALUES ('Water Bottle', 1);
INSERT INTO `wish_list`(gift_name, member_id) VALUES ('Agenda', 2);
INSERT INTO `wish_list`(gift_name, member_id) VALUES ('Strawberries', 3);
INSERT INTO `wish_list`(gift_name, member_id) VALUES ('Mandolin', 4);
INSERT INTO `wish_list`(gift_name, member_id) VALUES ('Mew pokemon', 5);

INSERT INTO `profession`(`profession_name`) VALUES ('accountant');
INSERT INTO `profession`(`profession_name`) VALUES ('administrative services department manager');
INSERT INTO `profession`(`profession_name`) VALUES ('agricultural advisor');
INSERT INTO `profession`(`profession_name`) VALUES ('air-conditioning installer');
INSERT INTO `profession`(`profession_name`) VALUES ('mechanic');
INSERT INTO `profession`(`profession_name`) VALUES ('aircraft service technician');
INSERT INTO `profession`(`profession_name`) VALUES ('ambulance driver (non paramedic)');
INSERT INTO `profession`(`profession_name`) VALUES ('animal carer (not in farms)');
INSERT INTO `profession`(`profession_name`) VALUES ('arable farm manager, field crop or vegetable');
INSERT INTO `profession`(`profession_name`) VALUES ('arable farmer, field crop or vegetable');
INSERT INTO `profession`(`profession_name`) VALUES ('architect');
INSERT INTO `profession`(`profession_name`) VALUES ('asbestos removal worker');
INSERT INTO `profession`(`profession_name`) VALUES ('assembler');
INSERT INTO `profession`(`profession_name`) VALUES ('assembly team leader');
INSERT INTO `profession`(`profession_name`) VALUES ('bank clerk (back-office)');
INSERT INTO `profession`(`profession_name`) VALUES ('beauty therapist');
INSERT INTO `profession`(`profession_name`) VALUES ('beverage production process controller');
INSERT INTO `profession`(`profession_name`) VALUES ('boring machine operator');
INSERT INTO `profession`(`profession_name`) VALUES ('bricklayer');
INSERT INTO `profession`(`profession_name`) VALUES ('butcher');
INSERT INTO `profession`(`profession_name`) VALUES ('car mechanic');
INSERT INTO `profession`(`profession_name`) VALUES ('carpenter');
INSERT INTO `profession`(`profession_name`) VALUES ('charge nurse');
INSERT INTO `profession`(`profession_name`) VALUES ('check-out operator');
INSERT INTO `profession`(`profession_name`) VALUES ('child care services manager');
INSERT INTO `profession`(`profession_name`) VALUES ('child-carer');
INSERT INTO `profession`(`profession_name`) VALUES ('civil engineering technician');
INSERT INTO `profession`(`profession_name`) VALUES ('cleaning supervisor');
INSERT INTO `profession`(`profession_name`) VALUES ('climatologist');
INSERT INTO `profession`(`profession_name`) VALUES ('cloak room attendant');
INSERT INTO `profession`(`profession_name`) VALUES ('cnc operator');
INSERT INTO `profession`(`profession_name`) VALUES ('community health worker');
INSERT INTO `profession`(`profession_name`) VALUES ('company director, 10-50 employees');
INSERT INTO `profession`(`profession_name`) VALUES ('confectionery maker');
INSERT INTO `profession`(`profession_name`) VALUES ('construction operative');
INSERT INTO `profession`(`profession_name`) VALUES ('cooling or freezing installer or mechanic');
INSERT INTO `profession`(`profession_name`) VALUES ('database designer');
INSERT INTO `profession`(`profession_name`) VALUES ('dental hygienist');
INSERT INTO `profession`(`profession_name`) VALUES ('dental prosthesis technician');
INSERT INTO `profession`(`profession_name`) VALUES ('department store manager');
INSERT INTO `profession`(`profession_name`) VALUES ('dietician');
INSERT INTO `profession`(`profession_name`) VALUES ('display designer');
INSERT INTO `profession`(`profession_name`) VALUES ('domestic housekeeper');
INSERT INTO `profession`(`profession_name`) VALUES ('education advisor');
INSERT INTO `profession`(`profession_name`) VALUES ('electrical engineer (professional)');
INSERT INTO `profession`(`profession_name`) VALUES ('electrical mechanic or fitter');
INSERT INTO `profession`(`profession_name`) VALUES ('engineering maintenance supervisor');
INSERT INTO `profession`(`profession_name`) VALUES ('estate agent');
INSERT INTO `profession`(`profession_name`) VALUES ('executive secretary');
INSERT INTO `profession`(`profession_name`) VALUES ('felt roofer');
INSERT INTO `profession`(`profession_name`) VALUES ('filing clerk');
INSERT INTO `profession`(`profession_name`) VALUES ('financial clerk');
INSERT INTO `profession`(`profession_name`) VALUES ('financial services manager');
INSERT INTO `profession`(`profession_name`) VALUES ('fire fighter');
INSERT INTO `profession`(`profession_name`) VALUES ('first line supervisor beverages workers');
INSERT INTO `profession`(`profession_name`) VALUES ('first line supervisor of cleaning workers');
INSERT INTO `profession`(`profession_name`) VALUES ('flight attendant');
INSERT INTO `profession`(`profession_name`) VALUES ('floral arranger');
INSERT INTO `profession`(`profession_name`) VALUES ('food scientist');
INSERT INTO `profession`(`profession_name`) VALUES ('garage supervisor');
INSERT INTO `profession`(`profession_name`) VALUES ('gardener, all other');
INSERT INTO `profession`(`profession_name`) VALUES ('general practitioner');
INSERT INTO `profession`(`profession_name`) VALUES ('hairdresser');
INSERT INTO `profession`(`profession_name`) VALUES ('head groundsman');
INSERT INTO `profession`(`profession_name`) VALUES ('horse riding instructor');
INSERT INTO `profession`(`profession_name`) VALUES ('hospital nurse');
INSERT INTO `profession`(`profession_name`) VALUES ('hotel manager');
INSERT INTO `profession`(`profession_name`) VALUES ('house painter');
INSERT INTO `profession`(`profession_name`) VALUES ('hr manager');
INSERT INTO `profession`(`profession_name`) VALUES ('it applications programmer');
INSERT INTO `profession`(`profession_name`) VALUES ('it systems administrator');
INSERT INTO `profession`(`profession_name`) VALUES ('journalist');
INSERT INTO `profession`(`profession_name`) VALUES ('judge');
INSERT INTO `profession`(`profession_name`) VALUES ('kitchen assistant');
INSERT INTO `profession`(`profession_name`) VALUES ('lathe setter-operator');
INSERT INTO `profession`(`profession_name`) VALUES ('lawyer');
INSERT INTO `profession`(`profession_name`) VALUES ('legal secretary');
INSERT INTO `profession`(`profession_name`) VALUES ('local police officer');
INSERT INTO `profession`(`profession_name`) VALUES ('logistics manager');
INSERT INTO `profession`(`profession_name`) VALUES ('machine tool operator');
INSERT INTO `profession`(`profession_name`) VALUES ('manager, all other health services');
INSERT INTO `profession`(`profession_name`) VALUES ('meat processing operator');
INSERT INTO `profession`(`profession_name`) VALUES ('mechanical engineering technician');
INSERT INTO `profession`(`profession_name`) VALUES ('medical laboratory technician');
INSERT INTO `profession`(`profession_name`) VALUES ('medical radiography equipment operator');
INSERT INTO `profession`(`profession_name`) VALUES ('metal moulder');
INSERT INTO `profession`(`profession_name`) VALUES ('metal production process operator');
INSERT INTO `profession`(`profession_name`) VALUES ('meteorologist');
INSERT INTO `profession`(`profession_name`) VALUES ('midwifery professional');
INSERT INTO `profession`(`profession_name`) VALUES ('mortgage clerk');
INSERT INTO `profession`(`profession_name`) VALUES ('musical instrument maker');
INSERT INTO `profession`(`profession_name`) VALUES ('non-commissioned officer armed forces');
INSERT INTO `profession`(`profession_name`) VALUES ('nursery school teacher');
INSERT INTO `profession`(`profession_name`) VALUES ('nursing aid');
INSERT INTO `profession`(`profession_name`) VALUES ('ophthalmic optician');
INSERT INTO `profession`(`profession_name`) VALUES ('payroll clerk');
INSERT INTO `profession`(`profession_name`) VALUES ('personal carer in an institution for the elderly');
INSERT INTO `profession`(`profession_name`) VALUES ('personal carer in an institution for the handicapped');
INSERT INTO `profession`(`profession_name`) VALUES ('personal carer in private homes');
INSERT INTO `profession`(`profession_name`) VALUES ('personnel clerk');
INSERT INTO `profession`(`profession_name`) VALUES ('pest controller');
INSERT INTO `profession`(`profession_name`) VALUES ('physician assistant');
INSERT INTO `profession`(`profession_name`) VALUES ('pipe fitter');
INSERT INTO `profession`(`profession_name`) VALUES ('plant maintenance mechanic');
INSERT INTO `profession`(`profession_name`) VALUES ('plumber');
INSERT INTO `profession`(`profession_name`) VALUES ('police inspector');
INSERT INTO `profession`(`profession_name`) VALUES ('policy advisor');
INSERT INTO `profession`(`profession_name`) VALUES ('post secondary education teacher');
INSERT INTO `profession`(`profession_name`) VALUES ('post sorting or distributing clerk');
INSERT INTO `profession`(`profession_name`) VALUES ('power plant operator');
INSERT INTO `profession`(`profession_name`) VALUES ('primary school head');
INSERT INTO `profession`(`profession_name`) VALUES ('primary school teacher');
INSERT INTO `profession`(`profession_name`) VALUES ('printing machine operator');
INSERT INTO `profession`(`profession_name`) VALUES ('psychologist');
INSERT INTO `profession`(`profession_name`) VALUES ('quality inspector');
INSERT INTO `profession`(`profession_name`) VALUES ('receptionist');
INSERT INTO `profession`(`profession_name`) VALUES ('restaurant cook');
INSERT INTO `profession`(`profession_name`) VALUES ('road paviour');
INSERT INTO `profession`(`profession_name`) VALUES ('roofer');
INSERT INTO `profession`(`profession_name`) VALUES ('sailor');
INSERT INTO `profession`(`profession_name`) VALUES ('sales assistant, all other');
INSERT INTO `profession`(`profession_name`) VALUES ('sales or marketing manager');
INSERT INTO `profession`(`profession_name`) VALUES ('sales representative');
INSERT INTO `profession`(`profession_name`) VALUES ('sales support clerk');
INSERT INTO `profession`(`profession_name`) VALUES ('seaman (armed forces)');
INSERT INTO `profession`(`profession_name`) VALUES ('secondary school manager');
INSERT INTO `profession`(`profession_name`) VALUES ('secondary school teacher');
INSERT INTO `profession`(`profession_name`) VALUES ('secretary');
INSERT INTO `profession`(`profession_name`) VALUES ('security guard');
INSERT INTO `profession`(`profession_name`) VALUES ('sheet metal worker');
INSERT INTO `profession`(`profession_name`) VALUES ('ship mechanic');
INSERT INTO `profession`(`profession_name`) VALUES ('shoe repairer, leather repairer');
INSERT INTO `profession`(`profession_name`) VALUES ('social photographer');
INSERT INTO `profession`(`profession_name`) VALUES ('soldier');
INSERT INTO `profession`(`profession_name`) VALUES ('speech therapist');
INSERT INTO `profession`(`profession_name`) VALUES ('steel fixer');
INSERT INTO `profession`(`profession_name`) VALUES ('stockman');
INSERT INTO `profession`(`profession_name`) VALUES ('structural engineer');
INSERT INTO `profession`(`profession_name`) VALUES ('surgeon');
INSERT INTO `profession`(`profession_name`) VALUES ('surgical footwear maker');
INSERT INTO `profession`(`profession_name`) VALUES ('swimming instructor');
INSERT INTO `profession`(`profession_name`) VALUES ('tailor, seamstress');
INSERT INTO `profession`(`profession_name`) VALUES ('tax inspector');
INSERT INTO `profession`(`profession_name`) VALUES ('taxi driver');
INSERT INTO `profession`(`profession_name`) VALUES ('tile layer');
INSERT INTO `profession`(`profession_name`) VALUES ('transport clerk');
INSERT INTO `profession`(`profession_name`) VALUES ('travel agency clerk');
INSERT INTO `profession`(`profession_name`) VALUES ('truck driver long distances');
INSERT INTO `profession`(`profession_name`) VALUES ('university professor');
INSERT INTO `profession`(`profession_name`) VALUES ('university researcher');
INSERT INTO `profession`(`profession_name`) VALUES ('veterinary practitioner');
INSERT INTO `profession`(`profession_name`) VALUES ('vocational education teacher');
INSERT INTO `profession`(`profession_name`) VALUES ('waiting staff');
INSERT INTO `profession`(`profession_name`) VALUES ('web developer');
INSERT INTO `profession`(`profession_name`) VALUES ('welder, all other');
INSERT INTO `profession`(`profession_name`) VALUES ('wood processing plant operator');
