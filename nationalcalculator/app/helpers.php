<?php

Blade::extend(function($value)
{
	return preg_replace('/(\s*)@(break|continue)(\s*)/', '$1<?php $2; ?>$3', $value);
});

HTML::macro('menuItem', function($name, $to, $options = []) {

    if (!isset($options['is'])) {
        $is = trim($to, "/");
    } elseif ($options['is'] == "*") {
        $is = trim($to, "/")."*";
    } else {
        $is = $options['is'];
    }

    $class = isset($options['class']) ? $options['class'] : "";

    $active = "";
    if ( call_user_func_array(array('Request','is'), (array)$is) ) {
        $active = " active";
    }

    if (strpos($to, "@") !== false) {
        $route = URL::action($to);
    } else {
        $route = URL::to($to);
    }

   echo "<li class='".$active."'><a class='".$class."' href='" . $route . "'>$name</a></li>";

});

HTML::macro('content', function($key) {
    $content = Models\Content::firstOrNew(['key'=>$key]);

    if ($content->exists) {
        $html = $content->content;
    } else {
        $html = "Please create content key '$key'";
    }

    return $html;

});

//if ( ! function_exists('e'))
//{
//    /**
//     * Escape HTML entities in a string.
//     *
//     * @param  string  $value
//     * @return string
//     */
//    function e($value)
//    {
//        if ((string) $value) {
//            return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
//        } else {
//            return $value;
//        }
//    }
//}


//
//
//rename_function('call_user_func_array', 'old_call_user_func_array');
//
//override_function('call_user_func_array', '$callback,$array', 'return override_call_user_func_array($callback,$array)');
//
//function override_call_user_func_array($callback, $parameters) {
//    if (is_array($callback)) {
//        $obj = $callback[0]
//        $method = $callback[1];
//    } else { return old_call_user_func_array($callback, $parameters); }
//
//    switch(count($parameters))
//    {
//        case 0:
//            $response = $obj->$method();
//            break;
//        case 1:
//            $response = $obj->$method($parameters[0]);
//            break;
//        case 2:
//            $response = $obj->$method($parameters[0], $parameters[1]);
//            break;
//        case 3:
//            $response = $obj->$method($parameters[0], $parameters[1], $parameters[2]);
//            break;
//        case 4:
//            $response = $obj->$method($parameters[0], $parameters[1], $parameters[2], $parameters[3]);
//            break;
//        case 5:
//            $response = $obj->$method($parameters[0], $parameters[1], $parameters[2], $parameters[3], $parameters[4]);
//            break;
//        default:
//            $response = old_call_user_func_array(array($obj, $method), $parameters);
//    }
//
//    return $response;
//}