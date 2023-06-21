# School-Ticket-System

## Project description (german)

An unserer Schule sind inzwischen fast alle Räume mit digitalen Medien ausgestattet, wie sie in den beiden angehängten
Präsentationen beschrieben sind. Bei der Verwendung der Medien kann es zu zahlreichen technischen Fehlern und Problemen
kommen. Zum Beispiel wird häufig das Bild vom Gerät nicht auf den Beamer übertragen. Dahinter können Bedienungsfehler
stecken, jedoch auch falsch gesteckte oder defekte Kabel. Kann ein solches Problem nicht vom Lehrer gelöst werden,
wendet sich dieser per Mail an den Schulsupport. Da diese Fehlermeldungen häufig sehr unspezifisch sind und Rückfragen
erfordern, soll für den Support ein Ticket-System entwickelt werden.
Die Idee ist dabei folgende: Lehrer die bei Ihrer Arbeit auf einen technischen Fehler stoßen, den sie selbst nicht lösen
können, greifen mit Hilfe eines Links auf das Ticket-System zu. In einem Formular
können Sie den Raum und die Geräte auswählen, bei denen der Fehler aufgetreten ist und diesen beschreiben bzw. eine
vorformulierte Fehlerbeschreibung auswählen. Die Fehlermeldung (das Ticket) soll in einer Datenbank gespeichert werden.
Der Support greift über einen anderen Link auf das System zu und bekommt die Fehlermeldungen übersichtlich angezeigt.
Nachdem ein Ticket vom Support bearbeitet wurde, erhält der Lehrer, der es angefordert hat, eine Rückmeldung.

## Project description (english)

Almost all rooms at our school are now equipped with digital media, as described in the two attached presentations. When
using the media, there can be numerous technical errors and problems. For example, the image from the device is often
not transmitted to the projector. This can be due to operating errors, but also to incorrectly plugged or defective
cables. If such a problem cannot be solved by the teacher, he or she contacts the school support by e-mail. Since these
error messages are often very unspecific and require queries, a ticket system is to be developed for the support.
The idea is as follows: Teachers who encounter a technical error in their work that they cannot solve themselves access
the ticket system using a link. In a form, they can select the room and devices where the error occurred and describe it
or select a pre-formulated error description. The error message (the ticket) should be stored in a database. The support
accesses the system via another link and gets the error messages displayed clearly. After a ticket has been processed by
the support, the teacher who requested it receives feedback.

## About the project

This project was created as a part of my "Leistungskurs Informatik" (advanced course computer science) in the 12th grade
of my school. Don't expect too much from it, it's just a school project.

## How does it run?

I use php and MariaDB for this project. You can run it with XAMPP or something similar. (A Docker-Compose is also
included).

In terms of Front-End, I use tabler.io, which is a free CSS framework.

For the backend, I use PHP with composer. If you take a look at the code structure, you'll find a `config` and
a `modules` directory. The config directory contains the configuration files for the backend and cannot be accessed from the
outside. The modules directory contains the modules of the backend. In here, we also have the html snippets used for
less code duplication and a smoother development experience.

For authentication, I use PHP's session management. A user can log in with his username and password or a magic link. 
The latter is also used while registering a new user. 

Emails are based on the PHPMailer library which is included via composer. For speed, the emails are sent asynchronously
with a table in the database acting as a cache. 

In terms of security, I use prepared statements for all database queries to prevent XSS attacks. I also use the
`password_hash` function to hash the passwords with a random salt.

The code is structured using PHP namespaces and a function for auto-loading all needed dependencies.
I mostly use the PSR-4 standard for naming the namespaces. 
Also, the project uses php classes for more flexibility and less code duplication.

To create the initial user, first configure your database connection in the `src/config` directory. Then, run the
`init.php` script in the `src` directory. This will create the database tables and the initial user with administrative
privileges. The password can be set after the first login using a magic link provided by the script.

## ER-Diagram & Relational Model

The ER-Diagram is located under `er.drawio` in the root directory of the project.

The Relational Model is located under `relational.md` in the root directory of the project.
