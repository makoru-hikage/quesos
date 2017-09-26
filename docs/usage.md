# How to use Quesos?
___
### Installation
I repeat:
Just assure that you have composer and do this `composer require "makoru-hikage/quesos"`

Or just download the ZIP from github and do `require` the `QueryConverter.php`
___
### Usage
If you have used Composer, just do write
```
<?php
require __DIR__.'/vendor/autoload.php';
use Quesos\QueryStringConverter;
```
#### How do I deal with the raw query strings first?
You must parse the query strings to derive an associative array from it. For this tool to facilitate search, it must be supplied with an associative array. There are many ways and tools to do it.

##### Good ol' plain PHP

```
//I used 'if' because $_SERVER['QUERY_STRING']
//can be null and we don't want to spew errors
//Read more about parse_str in www.php.net
//should you expect 
//"$testArray = parse_str($_SERVER['QUERY_STRING'], $testArray);"

$testArray =  [];
if ($_SERVER['QUERY_STRING']) {
	parse_str($_SERVER['QUERY_STRING'], $testArray);
}
```
##### Slim Framework
`$request->getQueryParams()`
##### Laravel
`$request->query();`
##### Others
The point is RTFM OF WHATEVER FRAMEWORK YOU ARE USING for each of them has feature for parsing query strings.
___

### I have the means to parse the query string... What should be input?
It should look like this:
`?key=operator,value` or `?key=value` 
and it'll be transformed like this: 
`"key" => [ "key", "operator", "value" ]` or `"key" => [ "key", "=", "value" ]`
(The absence of `operator` just means "equal to")

There are various situations to deal with. There could be mere search by a unique identifier (`username = "christina_moran"`), comparison against a number (`casino_player_age >= 21`), range of values (`yr_lvl between [2,3]`), or a set of values (`women in ["Aida", "Lorna", "Fe"]`)
___

#### Simplest criterion
"I want to search a guy with a username 'clito_reyes69'"

`yoursite.com/profile/?username=clito_reyes69` or `yoursite.com?username=eq,clito_reyes69`

which will result in a following value

`"username" => [ "username", "=", "clito_reyes69" ]`
___

#### Negated simplest criterion
"I want to search students whose status are not 'EXPELLED'"

`yoursite.com/students/?student_status=ne,EXPELLED`

which will result in a following value

`"student_status" => [ "student_status", "!=", "EXPELLED" ]`
___

#### Involvement of Comparative Operators.
Comparative operators are `>, <, >=, <=, !=, and =`. Usually good when dealing with numbers. Respectively, `gt, lt, gte, lte, eq, and ne` when they'll be in a query string.

"Students under 18 require parent's waiver for the field trip."

`yoursite.com/students/?age=lt,18`

which will result in a following value

`"age" => [ "age", "<", "18" ]`
___

#### The Value is actually an array
There'll be time that your input for a single attribute is an array. This program automatically use `in` instead.

"I want these men: Kanor, Hayden, Chito, Wally, Paolo"

`yoursite.com/idols/?first_name=Kanor,Hayden,Chito,Wally,Paolo` or
`yoursite.com/idols/?first_name=in,Kanor,Hayden,Chito,Wally,Paolo`

which will result in a following value

`"first_name" => [ "first_name", "in", [ "Kanor", "Hayden", "Chito", "Wally", "Paolo"] ]`

To have to opposite, just put `nin` for `not in`.
___

#### Range of numbers
It can be 1 to N or A to Z, none of my business. The value must be an array of two elements: the high and low, or low and high, whatever.

"I want students who are full of potentials, students of Grade 11 and 12"

`yoursite.com/students/?grade_level=bwn,11,12`

which will result in a following value

`"grade_level" => [ "grade_level", "between", [ 11,12 ] ]`

To have to opposite, just put `nbwn` for `not between`.
___

#### LIKE operator
A special kind of operator. See MySQL LIKE operator for more details.

"I want people with '-maru' at the end of their name"

`yoursite.com/users/?first_name=like,%25maru`

which will result in a following value

`"first_name" => [ "first_name", "like", %maru ]`

To have to opposite, just put `nlike` for `not like`.
(Sorry for not having support for regex yet. Please do any workaround)
___

### With all that fuss, What shall I do now?
Just call the class statically, no need for instantiating it. Just copy-paste the snippet to a fresh empty `index.php` in a folder and `cd` to that folder and run `php -S localhost:3000 -t './'` for a mini server to test with your browser to test my tool.

```
<?php

require __DIR__.'/vendor/autoload.php';
//or require '/path/to/QueryStringConverter.php';
//if you didn't use composer

use Quesos\QueryStringConverter;

$parsedQueryString = [];

if ($_SERVER['QUERY_STRING']) {
	parse_str($_SERVER['QUERY_STRING'], $parsedQueryString);
}

$o = QueryStringConverter::convert($parsedQueryString);
echo json_encode($o, JSON_PRETTY_PRINT);
```
___

### Does this work on my framework or my ORM?
Yes, as long as it is PHP. This thing is framework-agnostic and has no dependency. What you do with the output is none of my business, although it can help you. For example. I have this search criteria (Don't expect to understand this if you are not a Laravel user):
```
$outputFromQueryConverter = [
    "age" => [ "age", ">", 18 ],
    "grade_level" => ["grade_level", "=", 11]
];

//Will return an error
$students = Student::where($outputFromQueryConverter);

//Remove the keys, so indexed array of arrays. Columns still intact.
$outputFromQueryConverter = array_values($outputFromQueryConverter);

//YAY! This is in Laravel Docs, look at
// "https://laravel.com/docs/5.5/queries#where-clauses"
$students = Student::where($outputFromQueryConverter);
```

