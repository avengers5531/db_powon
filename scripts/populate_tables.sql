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
  create your own groups and add friends to see their posts.
  Admin', 'V', NULL, -1, 6);

COMMIT;
