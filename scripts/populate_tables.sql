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
INSERT INTO profession(profession_name) VALUES ('Software Developer');
INSERT INTO profession(profession_name) VALUES ('Student');
INSERT INTO member(member_id, username, password,
                   first_name, last_name,
                   user_email, date_of_birth, is_admin,
                   status, region_access, lives_in, professions_access,
                   interests_access,
                   dob_access, email_access,
                   profile_picture)
VALUES (1, 'johnsmith',
  '$2y$10$bWyUqHoKg2USEITqwBjAyOSdrcZRve609wsIL27EumfKtGqCEMuOC',
  'John',
  'Smith',
  'johnsmith@warmup.project.ca',
  '1990-06-12', 'N', 'A', -1,3, -1, -1, -1, -1, NULL);

INSERT INTO member(member_id, username, password,
                   first_name, last_name,
                   user_email, date_of_birth, is_admin,
                   status, region_access, lives_in, professions_access,
                   interests_access,
                   dob_access, email_access,
                   profile_picture)
VALUES (2, 'ndalo',
           '$2y$10$WLHWP7PM/Y6ozx6dKu7Nt.VzCsFTGGmajFUN8dXCODpFA6Ra8qixy',
           'Ndalo',
           'Zolani',
           'ndalo.zolani@warmup.project.ca',
           '1989-12-13', 'N', 'A', -1,3, -1, -1, -1, -1, NULL);

INSERT INTO member(member_id, username, password,
first_name, last_name,
user_email, date_of_birth, is_admin,
status, region_access, lives_in, professions_access,
interests_access,
dob_access, email_access,
profile_picture)
VALUES (3, 'haruhisuzumiya',
'$2y$10$ouv/rRd9n0W7SZ2XpUL4O.FhWlfqCOQPXsY7Ni5IZvxYi/TAmqnJi',
'ハルヒ', '涼宮',
'suzumiya.haruhi@warmup.project.ca',
'1992-07-26', 'Y', 'A', -1,4, -1, -1, -1, -1, NULL);

INSERT INTO member(member_id, username, password,
                   first_name, last_name,
                   user_email, date_of_birth, is_admin,
                   status, region_access, lives_in, professions_access,
                   interests_access,
                   dob_access, email_access,
                   profile_picture)
VALUES (4, 'robertom',
           '$2y$10$WLHWP7PM/Y6ozx6dKu7Nt.VzCsFTGGmajFUN8dXCODpFA6Ra8qixy',
           'Roberto',
           'McDonald',
           'roberto.m@warmup.project.ca',
           '1959-04-08', 'N', 'I', -1,1, -1, -1, -1, -1, NULL);

INSERT INTO member(member_id, username, password,
                   first_name, last_name,
                   user_email, date_of_birth, is_admin,
                   status, region_access, lives_in, professions_access,
                   interests_access,
                   dob_access, email_access,
                   profile_picture)
VALUES (5, 'rohit',
           '$2y$10$WLHWP7PM/Y6ozx6dKu7Nt.VzCsFTGGmajFUN8dXCODpFA6Ra8qixy',
           'Rohit',
           'Singh',
           'rohit.singh@warmup.project.ca',
           '1994-10-17', 'N', 'S', -1,2, -1, -1, -1, -1, NULL);

INSERT INTO works_as(member_id, profession_name) VALUES (3, 'Student');
INSERT INTO works_as(member_id, profession_name) VALUES (4, 'Software Developer');
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
  (1, 'Lord of the Rings Fans', 'A relaxed group to share information about The Lord Of The Rings.', 3);

INSERT INTO powon_group(powon_group_id, group_title, description, group_owner)
VALUES
  (2, 'Project R', 'A mysterious group working on the so-called ''Project R''', 4);

INSERT INTO `is_group_member`(powon_group_id, member_id, approval_date) VALUES (1, 4, CURRENT_TIMESTAMP);
INSERT INTO `is_group_member`(powon_group_id, member_id, approval_date) VALUES (2, 4, CURRENT_TIMESTAMP);
INSERT INTO `is_group_member`(powon_group_id, member_id, approval_date) VALUES (2, 5, CURRENT_TIMESTAMP);

INSERT INTO `invoice` (amount_due, payment_deadline, billing_period_start,
                       billing_period_end, account_holder)
VALUES (32.00, '2016-07-12 00:00:00', CURRENT_TIMESTAMP, '2017-06-12 00:00:00', 4);

INSERT INTO `invoice` (amount_due, payment_deadline, billing_period_start,
                       billing_period_end, account_holder)
VALUES (32.00, '2016-07-12 00:00:00', CURRENT_TIMESTAMP, '2017-06-12 00:00:00', 2);

INSERT INTO `invoice` (amount_due, payment_deadline, billing_period_start,
                       billing_period_end, account_holder)
VALUES (32.00, '2016-07-12 00:00:00', CURRENT_TIMESTAMP, '2017-06-12 00:00:00', 1);

COMMIT;