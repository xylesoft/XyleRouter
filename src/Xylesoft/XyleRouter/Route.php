<?php

namespace Xylesoft\XyleRouter;

use Xylesoft\XyleRouter\Interfaces\MatchInterface;
use Xylesoft\XyleRouter\Interfaces\RequestInterface;
use Xylesoft\XyleRouter\Interfaces\RouteInterface;

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
    private $regexPattern;

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
     * @var \Xylesoft\XyleRouter\Interfaces\MatchInterface
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
     * @param Router $router
     */
    public function __construct($routePattern)
    {
        $this->routePattern = $routePattern;
    }

    /**
     * Responsible with converting the incoming route into a preg_* compatible regular expression.
     *
     * @param string $pattern
     *
     * @return string
     */
    private function parse($pattern)
    {
        $parameters = $optionalParameters = [];
//
//        // Extract the simple '(token:pattern)' parameters and turn into valid REGEX.
//        if (preg_match('#\([a-zA-Z0-9]+:#', $pattern)) {
//            preg_match_all('#\([a-zA-Z0-9]+:[^{^}]+\)#', $pattern, $parameters);
//
//            if (count($parameters) === 0) {
//                throw new \InvalidArgumentException(sprintf('One or more route parameter patterns (token:regex) is invalid: %s', $pattern));
//            }
//        }
//
//        // Extract the optional '{(token:pattern)}' parameters and turn into valid REGEX.
//        if (preg_match('#{#', $pattern)) {
//            preg_match_all('#{.+\(\s+:.+\).+}#', $pattern, $optionalParameters);
//
//            if (count($optionalParameters) === 0) {
//                throw new \InvalidArgumentException(sprintf('One or more optional route Patterns {(token:regex)} is invalid: %s', $pattern));
//            }
//        }

        // Get all {xxx} tokens.
        if (preg_match('#{\S+?\(?[a-zA-Z0-9]+\)?\S+?}#', $pattern)) {
        }

        return '#'.$pattern.'#';
    }

    /**
     * The allowed HTTP methods.
     *
     * @param array $allowedMethods
     *
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function cut($polarity)
    {
        $this->cut = $polarity;

        return $this;
    }

    /**
     * preg_match REGEX of the route.
     *
     * @param $url
     *
     * @return RouteInterface|bool
     */
    public function match(RequestInterface $request)
    {
        if (! $this->regexPattern) {
            $this->regexPattern = $this->parse($this->routePattern);
        }

        $url = $request->getUrl();
        $pattern = $this->getRoutePattern();

        return (preg_match($pattern, $url)) ? $this : false;
    }

    /**
     * Method for adding conditions to the tokens in a route pattern.
     *
     * @param string         $token    The name of the token in the route pattern.
     * @param bool           $optional Whether the token is optional or not, default: false
     * @param MatchInterface $matcher  A match class
     *
     * @return bool Whether the match was successful or not.
     */
    public function where($token, $optional = false, MatchInterface $matcher)
    {
        $this->tokens[$token] = $matcher;
        if ($optional) {
            $this->optionalTokens[] = $token;
        }
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
        return $this->routePattern;
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
