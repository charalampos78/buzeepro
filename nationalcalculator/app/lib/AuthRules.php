<?php

namespace My;

use Illuminate\Routing\Route;
use Auth;
use Config;
use Log;
use Request;

class AuthRules {

    /**
     * ACCESS RULES INFO
     *
     * To initialize, create a new instance and send a copy of the controller $this that contains the rules,
     * the before() function is a good place to do this.
     * Then call the run() method.
     *
     * Access rules were based on the implementation used in Yii.
     * To expand please use the following as reference:
     * http://www.yiiframework.com/doc/guide/1.1/en/topics.auth#access-control-filter
     *
     * **Rules are processed in the order received, first match wins.**
     *
     * each controller should have:
     * public function accessRules() {
     * 	return array(
     * 	);
     * }
     * Where the array contains a list of rules such as the example rule.
     *
     * EXAMPLE rule, not all options implemented
     * array(
     * 	'allow',  // or 'deny'
     * 	// optional, list of action IDs (case insensitive) that this rule applies to
     * 	// if not specified, rule applies to all actions
     * 	'actions'=>array('edit', 'delete'),
     * 	// optional, list of controller IDs (case insensitive) that this rule applies to
     * 	'controllers'=>array('post', 'admin/user'),
     * 	// optional, list of usernames (case insensitive) that this rule applies to
     * 	// Use * to represent all users, ? guest users, and @ authenticated users
     * 	'users'=>array('thomas', 'kevin'),
     * 	// optional, list of roles (case sensitive!) that this rule applies to.
     * 	'roles'=>array('admin', 'editor'),
     * 	// optional, list of request types (case insensitive) that this rule applies to
     * 	'verbs'=>array('GET', 'POST'),
     * 	// This option is available since Yii version 1.1.1.
     * 	// optional, the customized error message to be displayed
     *  * 	'message'=>'Access Denied.',
     * 	// optional, list of IP address/patterns that this rule applies to
     * 	// e.g. 127.0.0.1, 127.0.0.*, *.hostname.com, othername.com
     * 	'ips'=>array('127.0.0.1', 'hostname.com'),
     * 	// optional, a PHP expression whose value indicates whether this rule applies
     * 	'expression'=>'!$user->isGuest && $user->level==2',
     * )
     *
     * @todo move this into its own module
     *
     */

    protected $_rules = [];
    protected $_rest = false;

    protected $_controller;
    protected $_action;

    /**
     * @property Route $_route
     */
    protected $_route;

    public $message = null;
    public $redirectUri;
    public $returnUri;


    /**
     * Default rule list made to be overridden in controllers
     *
     * @param Route $route
     *
     */
    public function __construct(Route $route) {

        $this->set_route_info($route);
        if ($this->_controller) {
            $rule_path = Config::get('authRules.rules.controller.' . $this->_controller, []);
        } else {
            $rule_path = Config::get('authRules.rules.uris', []);
        }

        $this->set_rules($rule_path);

    }

    public function set_route_info(Route $route) {

        $controller = null;
        $action = null;
        $uri = null;

        $pathInfo = $route->getActionName();

        if ($pathInfo != "Closure") {
            list($fullController, $action) = explode("@", $pathInfo);
            $fullController = explode('\\', $fullController);
            $controller = array_pop($fullController);
        } else {
            if ( ($parameters = $route->parameters()) && isset($parameters['controller']) ) {
                $controller = ucfirst($parameters['controller'])."Controller";
                if (!isset($parameters['action'])) $parameters['action'] = "index";
                $action = $parameters['action']."Action";
            } else {
                $uri = $route->getUri();
            }
        }

        $this->_uri = $uri;
        $this->_controller = $controller;
        $this->_action = strtolower($action);
        $this->_route = $route;

    }

    public function get_rules() {
        if ($rules = $this->_rules)
            return $rules;

        return [];
    }
    /**
     * Sets the rules and sets defaults in case items don't exist.
     *
     * @param array $rules list of rules
     *
     * @return void
     */
    public function set_rules($rules) {

        $rules = array_merge(Config::get('authRules.globalRules'), $rules);

        foreach ($rules as &$rule) {
            if (empty($rule['controllers']) || !is_array($rule['controllers'])) {
                $rule['controllers'] = array();
            }
            if (empty($rule['actions']) || !is_array($rule['actions'])) {
                $rule['actions'] = array();
            }
            if (empty($rule['verbs']) || !is_array($rule['verbs'])) {
                $rule['verbs'] = array();
            }

            //$rule['controllers'] = array_map('strtolower', $rule['controllers']);
            $rule['actions'] = array_map('strtolower', $rule['actions']);
            $rule['verbs'] = array_map('strtolower', $rule['verbs']);
        }

        $this->_rules = $rules;
    }


    /**
     * Checks and processes accessRules to determine anyones controller/action access
     */
    public function authorized() {
        //@todo caching a bit of this somewhere would be a good idea.

        $rule = $this->check_access($this->get_rules(), Config::get('authRules.defaultRule'));
//        Log::notice("Selected Access rule: ".print_r( $rule, true));
        return $this->process_rule($rule);

    }
    /**
     * Takes all rules, goes through each one and checks if it applys to current controller/action.
     * Then sends to matchRule to determine if that rule applies to current user.
     * Once match is found, it returns that rule.
     *
     * @param array $rules accessRules
     *
     * @return array    single access rule
     */
    public function check_access($rules, $default = array('deny')) {
        $match_rule = false;
        foreach ($rules as $rule) {
            $applicable = $this->applicable_rule($rule);
            if ( $applicable && ( $match_rule = $this->match_rule($rule) ) )
                break;
        }
        $match_rule = ($match_rule) ? $match_rule : $default;

        return $match_rule;
    }
    /**
     * Checks if the rule applies to the current controller/action/method
     *
     * @param accessRule $rule a single access rule
     *
     * @return bool    true of the rule applies, otherwise false
     */
    public function applicable_rule($rule) {
        if (
            (
                (	//if neither then matches all
                    empty($rule['actions'])
                    &&
                    empty($rule['controllers'])
                )
                ||
                (	//must match either a controller or action
                    (
                        empty($rule['controllers'])
                        &&
                        in_array(strtolower($this->_action),$rule['actions'])
                    )
                    ||
                    (
                        empty($rule['actions'])
                        &&
                        in_array(($this->_controller),$rule['controllers'])
                    )
                    ||
                    (
                        in_array(($this->_controller),$rule['controllers'])
                        &&
                        in_array(strtolower($this->_action),$rule['actions'])
                    )
                )
            )
            &&
            (
                //then only match a verb if it exists
                ( !empty($rule['verbs'])  && in_array(strtolower(Request::method()),$rule['verbs']) )
                ||
                empty($rule['verbs'])
            )
        )
            return true;
        else
            return false;
    }
    /**
     * Takes a rule and determines if it applys to the current user
     *
     * @param array $rule single access rule
     *
     * @return array/bool  access rule array if match found, otherwise false
     */
    public function match_rule($rule) {
        $match = true;
        //check if current user matches criteria based on user email or loggin status
        if (!empty($rule['users']) && is_array($rule['users'])) {
            switch ($rule['users'][0]) {
                case '*': //any user
                    $match = true;
                    break;
                case '?': //anon
                    $match = Auth::guest();
                    break;
                case '@': //authenticated
                    $match = Auth::check();
                    break;
                default://matches email (or username but it doesn't exists in e180)
                    $match = Auth::check()
                        && in_array(Auth::user()->email, $rule['users']);
                    break;
            }
        }
        //check if current user matches criteria based on their roles
        if ($match && !empty($rule['roles']) && is_array($rule['roles'])) {
            if (Auth::guest()) {
                $match = false;
            } else {
                foreach ($rule['roles'] as $role) {
                    if ($match = Auth::user()->hasRole($role)) break;
                }
            }
        }
        //check if current user matches criteria based on their ip
        if ($match && !empty($rule['ips']) && is_array($rule['ips'])) {
            $user_ip = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'127.0.0.1';
            $user_host = isset($_SERVER['REMOTE_HOST'])? strtolower($_SERVER['REMOTE_HOST']) : null;
            $match = false;
            foreach ($rule['ips'] as $ip) {
                if (preg_match('/[A-Za-z]/', $ip)) { //is a hostname
                    $ip = strtolower($ip);
                    if ($ip == $user_host || ( strpos($ip, '*') === 0 && substr_compare($user_host, str_replace('*', '', $ip), -1*(strlen($ip)-1),(strlen($ip)-1)) === 0 ) ) {
                        $match = true;
                        break;
                    }
                } else { //is ip
                    //see if ip matches or if begining of ip matches if wildcard at end
                    if ($ip == $user_ip || ( strpos($ip, '*') === (strlen($ip)-1) && strpos($user_ip, str_replace('*', '', $ip)) === 0 )) {
                        $match = true;
                        break;
                    }
                }
            }
        }
        //check if current user matches criteria based on a php expression
        if ($match && !empty($rule['expression'])) {
            //if the expression needs the $person and they are not logged in, then auto false
            if (stristr($rule['expression'],'$user') !== FALSE && !Auth::check()) {
                $match = false;
            } else {
                $match = $this->eval_expression($rule['expression'], Auth::user());
            }
        }

        return ($match) ? $rule : false;
    }
    /**
     * Determines what happens if a user is allowed or denied a page
     *
     * @param array $rule a specific access rule
     *
     * @return bool
     */
    public function process_rule($rule) {
        switch ($rule[0]) {
            case 'allow':
                if (!empty($rule['message'])) {
                    $this->message = $rule['message'];
                }

                return true;
                break;
            case 'deny':

                if ($this->_route->getName()) {
                    $this->returnUri = route($this->_route->getName());
                    $routeName = $this->_route->getName();
                } else {
                    $routeName = Request::path();
                }

                $this->message = str_replace('{route}', '"'.$routeName.'"', (!empty($rule['message']))?$rule['message']:Config::get('authRules.denyMessage') );
                $this->redirectUri = (!empty($rule['redirect'])) ? $rule['redirect'] : Config::get('authRules.denyRedirect');

                return false;
                break;
            default:
                return false;
        }
    }

    private function eval_expression($expression, $user) {
        return eval('return '.$expression.';' );
    }

}

