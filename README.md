Symfony test project
===========

A Symfony project created on August 19, 2018.

REST API for online service

SETUP:

    php app/console doctrine:database:create --env=dev --if-not-exists
    php app/console doctrine:schema:create --env=dev
    php app/console doctrine:migrations:migrate

Methods:
    
    POST /job/create 
    Content-Type: application/json    
    Expected result 201
    
    Request body:
        [
            {
                "category": 802030,
                "title": "Lorem ipsum dolor sit amet.",
                "zip": "10711",
                "description": "consectetur adipiscing",
                "dueDate": "2018-12-12"
            }
        ]  
        
     Result body:
        [
            {
                "category": 802030,
                "title": "Lorem ipsum dolor sit amet.",
                "zip": "10711",
                "description": "consectetur adipiscing",
                "dueDate": "2018-12-12"
            }
        ]     
 
    GET /job/list/{category}    
    Expected result 200
    
    Response body:
    
        [
            {
                "category": 108140,
                "title": "Umzug",
                "zip": "10711",
                "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla eget mattis neque. Pellentesque hendrerit turpis vitae diam commodo consequat a eget nunc. Sed eleifend molestie congue. Ut nec arcu ac justo luctus malesuada sit amet et metus..",
                "dueDate": "2018-12-12"
            },
            {
                "category": 108140,
                "title": "Umzug",
                "zip": "10711",
                "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla eget mattis neque. Pellentesque hendrerit turpis vitae diam commodo consequat a eget nunc. Sed eleifend molestie congue. Ut nec arcu ac justo luctus malesuada sit amet et metus..",
                "dueDate": "2018-12-12"
            },
            {
                "category": 108140,
                "title": "Umzug",
                "zip": "10711",
                "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla eget mattis neque. Pellentesque hendrerit turpis vitae diam commodo consequat a eget nunc. Sed eleifend molestie congue. Ut nec arcu ac justo luctus malesuada sit amet et metus..",
                "dueDate": "2018-12-12"
            }
        ]

    PUT /job/update/{job}    
    Content-Type: application/json
    Expected result 200
    
    Request body:
        [
            {
            	"title": "Lorem ipsum dolor sit amet",
                "description": "Lorem ipsum",
                "zip": "10707"
            }
        ]  
        
     Result body:
        [
            {
                "category": 802030,
                "title": "Lorem ipsum dolor sit amet",
                "zip": "10707",
                "description": "Lorem ipsum",
                "dueDate": "2018-12-12"
            }
        ]     

    DELETE /job/delete/{job}
    Expected result 204


Error cases:

| Error code  | Error message                               | 
| ----------- | ------------------------------------------- | 
| 110         | Category not found                          | 
| 111         | Only germans, please                        | 
| 112         | Title has to be between 5 and 50 characters | 
| 113         | Description is too long                     | 
| 114         | Due date cannot be in the past              | 
| 115         | No content                                  | 
| 116         | Cannot save job                             | 
| 117         | Job not found                               | 
| 117         | Job cannot be deleted                       | 
| 119         | Form is not valid                           | 


