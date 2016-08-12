README
======

Purpose
-------

A routing component to allow you to create a routing table which encompasses the following abilities:

* Groups

* Callback for match/unmatch based on tokens which must match data

* Cutting of URLs

* Defaulting of request values

* Matching of request headers

* URL matching with REGEX

* Tree structure which allows for easy configuration interpretations to be implemented

* Interfaces to implement your own moduling/controller calling.

Influence
---------

This routing implementation is heavily based upon the AgaviRouter (https://github.com/agavi/agavi) for inspiration.

Dependancy
----------

PHP >= 5.1.0


Process Flow
------------

    Boot Router
        -> Load Route definition
        -> Assign each route to the Router table
            -> Bootup and prepare matchers for individual Route.
            -> expose interpolation pattern for final generated route

    Request occurs [GET - /hello/jeramy]
        -> push Request into Router->dispatch();
        -> Loop Routing table
            -> Get route pull match pattern
                -> Matches
                    -> if stop route
                        -> Return route instance to dispatch()
                    -> if non-stop route
                        -> run route routine
                        -> continue Loop
                -> Doesn't Match
                    -> Continue Loop
            -> No more routes to match?
                -> Yes
                    -> continue loop.
                -> No
                    -> Break loop and return null from dispatch()
