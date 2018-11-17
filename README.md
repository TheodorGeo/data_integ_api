
# Open Source API for data integration & extraction (cloned from Gitlab)

This is a laravel 5.6 based API for data integration and extraction from the REST APIs of :

  - JIRA
  - Trello
  - Wrike
  - Asana

[This is a simple UI](https://gitlab.com/theodor_g/UI-Data-Integration-Testing) for testing this API and the generated JSON files

##### Basic documentation

# Trello

Before start using the API for making requests to trello you must define your Trello application key in the .env file :
`TRELLO_APP_KEY=your_trello_application_key` which you can find [here](http://https://trello.com/app-key "here")

Also the user's token is unique for each user and it can be found [here](https://trello.com/1/authorize?key=YOUR_TRELLO_APP_KEY&scope=read%2Cwrite&name=YOUR_APP_NAME&expiration=never&response_type=token)

Available routes for trello requests:

- Get all available boards via given token `api/trello/boards?token=users_token`
- Get the id of a board via the shortLink `api/trello/boards/id/{shortLink}?token=users_token`
- Get the prefered data of a board via the shortLink  `api/trello/board/shortlink/{shortlink}?token=users_token&fields=cards,lists,checklists,members`
- Get the prefered data of a board  via the Id of the board `api/trello/board/id/{id}?token=users_token&fields=cards,lists,checklists,members`

##### Available paramaters for the prefered data of the board :
- lists
- cards
- checklists
- members

At least 1 is required
# JIRA cloud & software
Requirements for making requests to JIRA's API:
- Domain (your-app-site.atlassian.net)
- Username or Email
- User's token that can be found [here](https://id.atlassian.com/manage/api-tokens?_ga=2.83521503.892353958.1532003274-1217404211.1532003274)

This API is using basic authetication provided by JIRA and it is explained [here](https://developer.atlassian.com/server/jira/platform/basic-authentication/)

Available routes for JIRA requests:

- Route to get all projects basic infos via user's token, domain and email  `api/jira/projects?token=users_token&email=user_email&domain=app_domain`
- Route to get a project infos via users' token, domain, email and project key `api/jira/projects/{projectKey}?token=users_token&email=user_email&domain=app_domain`
- Route to get a project's issues and associated users via users' token, domain, email and project key `api/jira/project/{projectKey}?token=users_token&email=user_email&domain=app_domain&fields=issues,users`

##### Available paramaters for the prefered data of each project :
- issues
- users

At least 1 is required

# Wrike
- To obtain access to wrike's API a user's token is required and it can be found [here](https://www.wrike.com/frontend/apps/index.html#/api) (permanent token)
- *Note that this is not the safest method and it is recommended to revoke the generated token after it's use*.

You can find more infos at [this](https://developers.wrike.com/documentation/oauth2#skipoauth) page !

Available routes for Wrike requests:
- Route to get all projects and folders basic infos via user's token `/api/wrike/folders?token=users_token`
- Route to get all tasks inside a project or a folder via user's token and project's/folder's id `/api/wrike/folder/{Folder's or Project's ID}?token=users_token&descendants=true&users=true&tasks=true`
- Route to get all tasks inside a project or a folder via user's token and project's/folder's name  `/api/wrike/folder?token=users_token&descendants=true&users=true&tasks=true&name=folder/project_name`
- Route to get all users / assignees inside a folder / project `/api/wrike/folder/{Folder's or Project's ID}/users?token=users_token&descendants=true`

##### Available paramaters for the prefered data of each project :
- tasks
- descendants (tasks or users)
- users (assignees in a folder/project)


# Asana
To obtain access to Asana's API you must generate a token by following the steps below :
```javascript
Click Avatar
My Profile Settings
Apps
Manage Developer Apps
Create New Personal Access Token
```
*Note that this is not the safest method and it is recommended to deauthorize the generated token after it's use*.

More infos can be found [here](https://asana.com/developers/documentation/getting-started/auth)

Available routes for Wrike requests:
- Route to get all projects basic infos via user's token `/api/asana/projects?token=users_token`
- Route to get all tasks or users inside a project via user's token and projects id `/api/asana/project/{Project's ID}?token=users_token&fields=users,tasks`
- Route to get all tasks inside a project via user's token and project's id  `/api/asana/project/{Project's ID}/tasks?token=users_token`
- Route to get all users  in a project's workspace `/api/asana/project/{Project's ID}/users?token=users_token`

##### Available paramaters for the prefered data of each project (2nd Route) :
- tasks
- users

License
----

MIT
