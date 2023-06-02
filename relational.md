# Relational Model

↑ [Back to README.md](README.md)


User(<u>user_id</u>, first_name, last_name, email, account_class, created_at)<br>
UserPassKey(<u>↑user_id</u>, password_hash, last_updated)<br>
UserMagicLinkKey(<u>↑user_id</u>, code)<br>

_To apply this model to a database, you can use sql scripts in the [sql](sql) directory._