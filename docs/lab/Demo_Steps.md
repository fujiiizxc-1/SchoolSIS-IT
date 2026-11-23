# Registration and academic records demonstration

Use only fictional records and the local SIS database.

Run `php artisan db:seed --class=DemoSchoolSeeder` in the local environment to create the assigned subject IS101 and the fictional accounts `teacher@sis.example.test` and `admin@sis.example.test`. Both use the local demonstration password `LocalDemo-2026!`.

1. Open the application URL and select **Register Student**. Enter Morgan Reyes, SIS-DEMO-001, BS Information Technology, and morgan.demo@example.test. Choose a new demo password and confirm it.
2. Submit the form and show the confirmation on the sign-in page. Sign in with the new account. Open **My Profile** and check the name, student ID, and course.
3. In the local MySQL client or GUI, query the linked rows. Change the email in the query if you used a different fictional account. The query reports whether a bcrypt hash is stored without displaying it.

   ```sql
   SELECT u.id, u.email, u.role,
          LEFT(u.password, 4) IN ('$2y$', '$2b$', '$2a$') AS password_is_bcrypt,
          s.user_id, s.student_number, s.course
   FROM users AS u
   JOIN students AS s ON s.user_id = u.id
   WHERE u.email = 'morgan.demo@example.test';
   ```

4. Sign out and sign in again with the new account. Open **My Profile** to confirm that the saved information is retrieved.
5. Open the subject list and enroll in a subject assigned to the demonstration teacher. Open **My Subjects** and verify the enrollment. Try enrolling in the same subject and term again.
6. Sign in as the assigned teacher and record a whole-number grade from 0 to 100 for that enrollment. Return to the student account and confirm the grade is visible in **My Grades**.
7. Sign in as an administrator and demonstrate subject creation or editing. Show that the student navigation offers only student functions.
8. Sign out. Submit a registration with a duplicate student ID and an unused email to show the validation error. Revisit a protected page while signed out and verify that the sign-in screen appears.

The browser communicates with Laravel over HTTP on the local machine. Laravel validates the request and sends the writes to MySQL. The confirmation appears after MySQL commits the linked account and profile. The architecture diagram shows both directions of communication.

## Recorded registration run

The tester registered fictional student `QA-20261123-01` in Chrome at a 320 px viewport. MySQL 8.4.13 returned one linked account and student profile with the `student` role and a bcrypt password hash. The tester signed out and back in and retrieved the same student ID and course.

The assigned teacher saved a grade of 94 for the student's IS101 enrollment. The student could view it, and MySQL confirmed the final grade. A separate enrollment stayed ungraded after an unassigned teacher's update was denied.

- [Registration on a phone-sized viewport](screenshots/registration-mobile.png)
- [Saved student profile on desktop](screenshots/profile-desktop.png)
- [Read-only student grade on desktop](screenshots/grades-desktop.png)
