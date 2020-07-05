<?php

namespace Tasteez\Models;


class Ingredient extends Model {

  public function getMealIngredients($mealID) {
    return $this->query("SELECT *
      FROM ingredients
      LEFT JOIN meal_ingredients
      ON ingredients.id = meal_ingredients.ingredient_id
      WHERE meal_id = ${mealID}");
  }

}
