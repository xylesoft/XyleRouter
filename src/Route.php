<?php

namespace Xylesoft\XyleRouter;

use Xylesoft\XyleRouter\Interfaces\TokenMatcherInterface;
use Xylesoft\XyleRouter\Interfaces\RequestInterface;
use Xylesoft\XyleRouter\Interfaces\RouteInterface;
use Xylesoft\XyleRouter\PatternParser\StandardRegex;

/**
 * Class Route.
 *
 * A single route definition class.
 */
class Route implements RouteInterface
{
    /**
     * The current route pattern.
     *
     * @var string
     */
    private $routePattern;

    /**
     * The current route as a full regular expression.
     *
     * @var string
     */
    private $regexPattern = null;

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
     * The pattern builder.
     *
     * @var PatternParser\StandardRegex
     */
    protected $parser;

    /**
     * Construct a new routing rule.
     *
     * @param string        $routePattern The matching pattern of the route.
     * @param string        $name         The unique name of the this route.
     * @param StandardRegex $parser
     */
    public function __construct($routePattern, $name, StandardRegex $parser = null)
    {
        $this->routePattern = $routePattern;
        $this->name($name);

        $this->parser = ($parser) ?: new StandardRegex();
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
     * The allowed HTTP methods.
     *
     * @param array $allowedMethods
     *
     * @return RouteInterface
     */
    public function methods(array $allowedMethods)
    {
        $this->methods = $allowedMethods;

        return $this;
    }

    /**
     * Callback that will be called if the route matches.
     *
     * @param callable $callable
     *
     * @return RouteInterface
     */
    public function handle(\Closure $callable)
    {
        $this->handler = $callable;

        return $this;
    }

    /**
     * In application name of the route.
     *
     * @param $name
     *
     * @return RouteInterface
     */
    public function name($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Default values for tokens in a URL if the value isn't present.
     *
     * @param array $defaults ['pattern token'=>'value', ...]
     */
    public function defaults(array $defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    /**
     * Whether the route stops here or carries on matching.
     *
     * @param $polarity
     *
     * @return RouteInterface
     */
    public function stop($polarity)
    {
        $this->stop = $polarity;

        return $this;
    }

    /**
     * Whether or not to cut out the matched portion of the URL from the current route.
     *
     * @param $polarity
     *
     * @return RouteInterface
     */
    public function cut($polarity)
    {
        $this->cut = $polarity;

        return $this;
    }

    /**
     * Method for adding conditions to the tokens in a route pattern.
     *
     * @param string                $token    The name of the token in the route pattern.
     * @param bool                  $optional Whether the token is optional or not, default: false
     * @param TokenMatcherInterface $matcher  A match class
     *
     * @return RouteInterface
     */
    public function where($token, $optional = false, TokenMatcherInterface $matcher)
    {
        $this->tokens[$token] = $matcher;
        if ($optional) {
            $this->optionalTokens[] = $token;
        }

        return $this;
    }

    /**
     * Perform a match routine against the request to see if the current route fulfills the outlined conditions.
     *
     * @param RequestInterface $url The request instance.
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

    /************************************
     * GETTER METHODS
     ************************************/

    /**
     * @return mixed
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRoutePattern()
    {
        return $this->parse($this->routePattern);
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @return boolean
     */
    public function getCut()
    {
        return $this->cut;
    }

    /**
     * @return boolean
     */
    public function getStop()
    {
        return $this->stop;
    }
}
