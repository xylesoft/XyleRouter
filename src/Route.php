<?php

namespace Xylesoft\XyleRouter;

use Xylesoft\XyleRouter\Interfaces\PatternParserInterface;
use Xylesoft\XyleRouter\Interfaces\RequestInterface;
use Xylesoft\XyleRouter\Interfaces\Route\RouteCuttingInterface;
use Xylesoft\XyleRouter\Interfaces\Route\RouteDefaultsInterface;
use Xylesoft\XyleRouter\Interfaces\Route\RouteMethodsInterface;
use Xylesoft\XyleRouter\Interfaces\Route\RouteNamingInterface;
use Xylesoft\XyleRouter\Interfaces\Route\RouteStoppingInterface;
use Xylesoft\XyleRouter\Interfaces\Route\RouteWhereInterface;
use Xylesoft\XyleRouter\Interfaces\RouteInterface;
use Xylesoft\XyleRouter\PatternParsers\LatinRegex;
use Xylesoft\XyleRouter\Traits\RouteCut;
use Xylesoft\XyleRouter\Traits\RouteDefaults;
use Xylesoft\XyleRouter\Traits\RouteMethods;
use Xylesoft\XyleRouter\Traits\RouteName;
use Xylesoft\XyleRouter\Traits\RouteWhere;
use Xylesoft\XyleRouter\Traits\RouteStop;

/**
 * Class Route.
 *
 * A single route definition class.
 */
class Route implements RouteInterface, RouteCuttingInterface, RouteStoppingInterface, RouteMethodsInterface, RouteDefaultsInterface, RouteWhereInterface, RouteNamingInterface
{

    use RouteCut,
        RouteName,
        RouteStop,
        RouteMethods,
        RouteDefaults,
        RouteWhere
    ;

    /**
     * The current route as a full regular expression.
     *
     * @var string
     */
    protected $regexPattern = null;

    /**
     * Array of tokens and the rules which apply to the token.
     *
     * @var array
     */
    protected $tokens = [];

    /**
     * Tokens which are considered optional in the pattern.
     *
     * @var array
     */
    protected $optionalTokens = [];

    /**
     * The allowed HTTP methods for the current route.
     *
     * @var array
     */
    protected $methods;

    /**
     * The handler of a successfully matched and stopped route.
     *
     * @var \Closure
     */
    protected $handler;

    /**
     * @var \Xylesoft\XyleRouter\Interfaces\TokenMatcherInterface
     */
    protected $callback;

    /**
     * Unique name of the route.
     *
     * @var string
     */
    protected $name;

    /**
     * The default value to be interpolated into a token if the token isn't provided a value.
     *
     * @var array
     */
    protected $defaults;

    /**
     * Whether to stop on this route if match was successful or not.
     *
     * @var bool
     */
    protected $stop = true;

    /**
     * Strip out the current route pattern before allowing the routing request to move to the next route check.
     *
     * @var bool
     */
    protected $cut = false;

    /**
     * The current route pattern.
     *
     * @var string
     */
    protected $routePattern;

    /**
     * The pattern builder.
     *
     * @var \Xylesoft\XyleRouter\PatternParsers\LatinRegex
     */
    protected $parser;

    /**
     * Construct a new routing rule.
     *
     * @param string $routePattern The matching pattern of the route.
     * @param string $name The unique name of the this route.
     * @param PatternParserInterface $parser
     */
    public function __construct($routePattern, $name, PatternParserInterface $parser = null) {

        $this->routePattern = $routePattern;
        $this->name($name);

        $this->parser = ($parser) ?: new LatinRegex();
    }

    /**
     * Callback that will be called if the route matches.
     *
     * @param \Closure $callable
     *
     * @return RouteInterface
     */
    public function handle(\Closure $callable) {

        $this->handler = $callable;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHandler() {

        return $this->handler;
    }

    /**
     * Build the REGEX pattern which will match against the incoming path.
     *
     * @param string $pattern
     *
     * @return string
     */
    private function parse($pattern)
    {
        if (! $this->regexPattern) {
            $this->regexPattern = $this->parser->parse($pattern, $this->tokens, $this->optionalTokens);
        }

        return $this->regexPattern;
    }

    /**
     * Perform a match routine against the request to see if the current route fulfills the outlined conditions.
     *
     * @param RequestInterface $request The request instance.
     *
     * @return RouteInterface|bool
     */
    public function match(RequestInterface $request)
    {
        if (preg_match_all($this->parse($this->routePattern), $request->getUrl(), $parameters)) {

            // initial matching succeeded, now time to check parameters
            if (count($this->tokens)) {
                foreach ($this->tokens as $tokenName => $tokenMatcher) {
                    $parameter = "";
                    if (array_key_exists($tokenName, $parameters) && mb_strlen($parameters[$tokenName][0]) > 0) {
                        $parameter = $parameters[$tokenName][0];
                        if (! $tokenMatcher->match($tokenName, $parameter, $request) && ! in_array($tokenName, $this->optionalTokens)) {

                            // deeper token value matching failed and was a required parameter.
                            return false;
                        }
                    } else {

                        // token now found in URI path. Check if optional.
                        if (! in_array($tokenName, $this->optionalTokens)) {

                            // not optional, route match failed.
                            return false;
                        }
                    }

                    // All tests passed. Now time to check whether a default should be put in place?
                    if (mb_strlen($parameters[$tokenName][0]) == 0 && array_key_exists($tokenName, $this->defaults)) {
                        $default = $this->defaults[$tokenName];
                        if (preg_match('#\((.+)\)#', $default, $match)) {
                            $parameter = $match[1];
                        }
                    }

                    // we can assume parameter is valid and should now be set against
                    // the request.
                    $request->setParameter($tokenName, $parameter);
                }
            }

            return $this;
        }

        // Matching failed.
        return false;
    }

    /**
     * @return string
     */
    public function getRoutePattern()
    {
        return $this->parse($this->routePattern);
    }
}
