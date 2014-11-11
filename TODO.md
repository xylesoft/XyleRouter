Todo for XyleRouter
====================

0.1.0
-----

    [x] Route Requestor.
    [ ] Regular Expression matching.
    [ ] Route result container.  (MatchedRoute class)
    [ ] Definition specifications.
    [x] Fluent definition interface.
    [ ] Basic Match callback.
    [ ] Route names.
    [ ] Contracts.
        [x] RequestInterface (wrapper for getting data from the implemented request)
        [x] MatchInterface (for callbacks.)
        [ ] RouteInterface (for creating other route classes which match other data, not just URLS, e.g. sources)
        [ ] MatchedRouteInterface (for when a route has been matched, the returned container by Router->dispatch())
    [ ] RouteSource class which acts like the Route class.

0.2.0
-----

    [ ] Route generator
    [ ] Grouping
    [ ] Composite Pattern for definition to allow for grouping


0.3.0
-----

    [ ] On callback based routes, break down the REGEX into smaller parts, so partial comparisons can be performed without having
        to call the callback method (potentially Database or more complex code)

1.0.0
-----

    [ ] Serialized route defintion