const scrapeIngredients = require('./scrape_ingredients.js')
const scrapeMeals = require('./scrape_meals.js')
const scrapeCategories = require('./scrape-catogories.js')

console.log("Scraping data...");


const fs = require('fs')
const mealsJSON = require('./data/meals.json')
const ingredientsJSON = require('./data/ingredients.json')
const meals = mealsJSON
const meal_ingredients = [];


meals.forEach(meal => {
    const meal_id = meal.id;
    const ingredients = meal.ingredients;
    const ingredients_ids = [];
    ingredients.forEach(a => {
            var found = ingredientsJSON.find(b => {
                if (b.name != undefined && a != undefined)
                    return b.name.toLowerCase() === a.toLowerCase()
                return false
            })
            if (!!found) {
                ingredients_ids.push(found.id)
            }
            meal_ingredients.push({
                meal_id: meal_id,
                ingredient_ids: ingredients_ids
            });
        })
    if (meal.id === "52806") {
        console.log(meal.ingredients)
        ingredients.forEach(a => {
                console.log(
                    ingredientsJSON.find(b => {
                        if (b.name != undefined)
                            return b.name.toLowerCase() === a.toLowerCase()
                        return false
                    })
                )


            })
    }

})

fs.writeFile("./data/meal_ingredients.json", JSON.stringify(meal_ingredients), function(err) {
})