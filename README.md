# Open Source API for data integration & extraction

This is a laravel 5.6 based API for data integration and extraction from the REST APIs of :

  - JIRA
  - Trello
  - Wrike
  - Asana

# Trello
Available routes for trello requests:

- Get all available boards via given token `/trello/boards?token=users_token`
- Get the id of a board via the shortLink `/trello/boards/id/{shortLink}?token=users_token`
- Get the prefered data of a board via the shortLink  `/trello/board/shortlink/{shortlink}?token=users_token&fields=cards,lists,checklists,members`
- Get the prefered data of a board  via the Id of the board `/trello/board/id/{id}?token=users_token&fields=cards,lists,checklists,members`

##### Available paramaters for the prefered data of the board :
- lists
- cards
- checklists
- members


License
----

MIT
