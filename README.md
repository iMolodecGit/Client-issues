
Endpoint create client:

POST
http://dev.canada.com:8083/client/create
{
    "name": "imolodec",
    "address": "internet"
}

POST
http://dev.canada.com:8083/issue/create
{
    "title" :  "заголовок, строка до 150 символов, без спецсимволов",
    "text": "текст проблемы, строка до 3000 символов, символы любые",
    "client_id" : 1,
    "in_work": false
}

PUT
http://dev.canada.com:8083/issue/in-work/1

GET
http://dev.canada.com:8083/issue/index

