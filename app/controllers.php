<?php

$container['TestAPIController'] = function($container) {
  return new \Tasteez\Controllers\Test($container);
};

// =============================================================================
// API Controllers
// =============================================================================

$container['MealsAPIController'] = function($container) {
  return new \Tasteez\Controllers\Api\Meals($container);
};

$container['MealAPIController'] = function($container) {
  return new \Tasteez\Controllers\Api\Meal($container);
};

$container['UserAPIController'] = function($container) {
  return new \Tasteez\Controllers\Api\User($container);
};

$container['ContactApiController'] = function($container) {
  return new \Tasteez\Controllers\Api\Contact($container);
};

$container['AuthAPIController'] = function($container) {
  return new \Tasteez\Controllers\Api\Auth($container);
};


// =============================================================================
// View Controllers
// =============================================================================

$container['AuthController'] = function($container) {
  return new \Tasteez\Controllers\Auth($container);
};

$container['AccountController'] = function($container) {
  return new \Tasteez\Controllers\Account($container);
};

$container['HomeController'] = function($container) {
  return new \Tasteez\Controllers\Home($container);
};

$container['SearchController'] = function($container) {
  return new \Tasteez\Controllers\Search($container);
};

$container['ContactController'] = function($container) {
  return new \Tasteez\Controllers\Contact($container);
};

$container['PrivacyPolicyController'] = function($container) {
  return new \Tasteez\Controllers\PrivacyPolicy($container);
};

$container['RecipeController'] = function($container) {
  return new \Tasteez\Controllers\Recipe($container);
};

$container['MealController'] = function($container) {
  return new \Tasteez\Controllers\Meal($container);
};

$container['MealsController'] = function($container) {
  return new \Tasteez\Controllers\Meals($container);
};
