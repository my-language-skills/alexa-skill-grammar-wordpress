# alexa-skill-grammar-wordpress

#Description:
- Creates meta boxes for importing content and translating for use in the amazon alexa skill "English Grammar".
- User imports and then translates all or specific sections from given markups configured in the settings admin page.
- After all desired chapters are imported and translated the admin user can export the data in a javascript file in a json format.
- Javascript file used as the content file for the amazon alexa skill.

#Details:
- Administrator user can block each chapter from the chapter page or block all chapters from admin page.
- Administrator user can hide or unhide all pictures information (url & preview).
- Administrator user can update translations strings.
- Administrator user can update content markups. 

#Warnings:
- Administrator must go first to Settings/Alexa-Input-Fields page to set the sections for importation.
- All missing information or inappropriate setups will prompt an alert with corresponding help messages.
- When adding the markups, the "<" and ">" characters are added automatically to disable any confusion and problems when storing and receiving from the database.