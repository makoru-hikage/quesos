# QUESOS
Query String Organized Search: A format to facilitate CRUD search through the use of URL query strings.

Please read the docs: [the introduction](docs/intro.md) and [the usage](docs/usage.md)

## Summary...

1. Receive an HTTP GET request laden with query string.
```
https://www.yoursite.com/contacts/?sex=female&points=gt,9000&first_name=Aida,Lorna,Fe&age=bwn,21,35
```
2. Let it be parsed anyway you like, either by vanilla PHP or any framework.
```
$rawQueryStr = $_SERVER['QUERY_STRING'];
$parsedUrlQuery = array();

parse_str($rawQueryStr, $parsedUrlQuery);

print_r($parsedUrlQuery, true);
```

OUTPUT:
```
Array
(
    [sex] => female
    [points] => gt,9000
    [first_name] => Aida,Lorna,Fe
    [age] => bwn,21,35
)
```
3. Use the tool itself.
```
$finishedProduct = QueryStringConverter::convert($parsedUrlQuery);

print_r($finishedProduct, true);
```
OUTPUT:
```
Array
(
    [sex] => Array
        (
            [0] => sex
            [1] => =
            [2] => female
        )

    [points] => Array
        (
            [0] => points
            [1] => <
            [2] => 9000
        )

    [first_name] => Array
        (
            [0] => first_name
            [1] => in
            [2] => Array
                (
                    [0] => Aida
                    [1] => Lorna
                    [2] => Fe
                )

        )

    [age] => Array
        (
            [0] => age
            [1] => between
            [2] => Array
                (
                    [0] => 21
                    [1] => 35
                )

        )

)
```
4. Do whatever you want with that.
