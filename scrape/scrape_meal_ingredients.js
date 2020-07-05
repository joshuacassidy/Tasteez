const fs = require('fs')
const mealsJSON = require('./data/meals.json')
const ingredientsJSON = require('./data/ingredients.json')
const ingredients = ingredientsJSON
const meals = mealsJSON
const meal_ingredients = [];

function scrapeMealIngredients(callback) {

    meals.forEach(meal => {
        const meal_id = meal.id;
        const ingredients = meal.ingredients;
        const ingredients_ids = [];
        ingredients.forEach(a => {
            var found = ingredientsJSON.find(b => b.name === a)
            if (!!found) {
                ingredients_ids.push(found.id)
            }
            meal_ingredients.push({
                meal_id: meal_id,
                ingredient_ids: ingredients_ids
            });
        })
        console.log(meal.id)
        if (meal.id === 52806) {

        }

    })

    fs.writeFile("./data/meal_ingredients.json", JSON.stringify(meal_ingredients), function(err) {
        if (err) return callback(err);
        callback(null)
    })
}