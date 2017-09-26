# Quesos

**Query String Organized Search: A format to facilitate CRUD search through the use of URL query strings.**

___
### Why is this created?
This is designed for the development of web APIs that has a filtered search feature. This facilitates the customized use of attributes, values, and comparative operators for in query strings, there is only key-value pair. 

For example, I am searching something in a student database for a students who completed their 2nd year (`yr_lvl >= 2`). Instead of writing `yr_lvl=>=2`* or `yr_lvl=%3E3D2`** in the query string itself, I'll just do `yr_lvl=gte,2`*** which is perfectly legal.

\* Comparison operators like ">", "<", etc.  are declared unsafe and are automatically escaped by some browsers or servers. Also, `=` is a reserved character.
\** You like memorizing ASCII and their hex form? And also, it looks gibberish to see escaped URL string beside a digit.
\*** It's easy to read and perfectly legal since it is legal to contain comma in a query value. `gte` is intuitive.

___
### Installation
Just assure that you have composer and do this `composer require "makoru-hikage/quesos"`

Or just download the ZIP from github and do `require` the `QueryConverter.php`

### Prerequisites
This is for web API developers. Just learn the fundamentals of HTTP and web development. Also you'll need to learn about HTTP REST specifications. And to use this, you must know PHP. 

(If you can port or have ported this to another language that can be used for HTTP, please notify me. You are encouraged to port this.)

Here's a primer for you should you have no idea: http://www.restapitutorial.com/

### How to use?
[Click here](usage.md)
___
### What are Query Strings?
Query String is the part of a URL that is usually started with a question mark (?) and a pair of key-values which is usually sent to a server for the sake of options, result filtering, or even redirection. And the key-value pairs are separated by ampersands (&). Between key and value is a simple equal operator (=).

For example: `https://www.google.com/search?tbm=isch&q=george+michael&tba=isz:l&tbs=ic:gray`

Where `https://www.google.com/search` is the URL and `?tbm=isch&q=george+michael&tba=isz:l&tbs=ic:gray` is the query string. If we are to look closely by separating them by &'s and ='s , here are the pairs:

- "tbm" = "isch"
- "q" = "george+michael"
- "tba" = "isz:l"
- "tbs" = "ic:gray"

If we refer to [this](https://stenevang.wordpress.com/2013/02/22/google-advanced-power-search-url-request-parameters/), It means "I am searching for images (`tbm=isch`) of George Michael (`q=george+michael`) that have large image size (`tba=isz:l`) and are in black and white (`ic:gray`). **In short, always check the reference of an API.** 

Why don't you try the example URL and paste it on your browser to make that common GET request?

___
### When to NOT use query strings?
When your API resource is simple such as a user searching for some students under a certain degree program such as BSIT.

If you are designing the API resource, it should look like this `http://www.exampleschool.com/students/BSIT` instead of making a user type this in browser `http://www.exampleschool.com/students?degree_code=BSIT`. Which is more disgusting? Should you have 4 or more search criteria, use query strings.

### When to use query strings?
When you are doing an advanced search where the end user will probably use a GUI or a webpage laden with forms and dropdowns such as that of advanced Google Image Search.

Ideally and commonly, the GUI forms the query strings then sends it via AJAX or normal means.

That is the situation where you must design an API resource that requires a GET request that has no Request Body and only contains a query string. 

(For me, a POST request with Request Body that contains the search criteria is disgusting. POST must be only used when an end user wants to really add something inside a server such as a new account)

Two things to consider:
1. Using GET method means that a must not change anything in the server ([RFC 2616, Section-4.3](https://tools.ietf.org/html/rfc2616#section-9.3))
2. Having a non-empty Request Body implies that a user wishes to change something inside the server.([RFC 2616, Section-4.3](https://tools.ietf.org/html/rfc2616#section-4.3))

Although the standards and specification is written, the implementor (API developer) can violate the standards, for they are merely standards. However, such standards are of a great help if followed.
___
### References:
- Google Search Parameters Cheat Sheet: https://stenevang.wordpress.com/2013/02/22/google-advanced-power-search-url-request-parameters/
- More about URI's and Query Strings (RFC 2396): https://www.ietf.org/rfc/rfc2396.txt
- HTTP Specifications: https://tools.ietf.org/html/rfc2616
- Escaped Characters: http://www.blooberry.com/indexdot/html/topics/urlencoding.htm
