# Relational Model

↑ [Back to README.md](README.md)

User(<u>user_id</u>, first_name, last_name, email, account_class, created_at)<br>
UserPassKey(<u>↑user_id</u>, password_hash, last_updated)<br>
UserMagicLinkKey(<u>↑user_id</u>, <u>code</u>)<br>
Ticket(<u>ticket_id</u>, title, created_at, status, ↑user_id, ↑room_id, ↑device_id)<br>
isMemberIn(<u>↑user_id</u>, <u>↑ticket_id</u>)<br>
Message(<u>message_id</u>, content, message_type, created_at, ↑user_id, ↑ticket_id)<br>
Room(<u>room_id</u>, building)<br>
Device(<u>device_id</u>, device_name, device_description)<br>
PresetMessage(<u>preset_id</u>, ↑device_id, name, content)<br>
ReadTo(<u>↑user_id</u>, <u>↑message_id</u>, <u>↑ticket_id</u>)<br>
Email(<u>email_id</u>, receiver, subject, content)<br>

_To apply this model to a database, you can use sql scripts in the [sql](sql) directory._