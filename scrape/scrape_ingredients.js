const fs = require('fs')
const rp = require('request-promise')
const mealdbIngredients = require('./data/mealdb/ingredients.json')

const meals = []
const firstMealID = 52764
const lastMealID = 52956
let failedRequests = 0


function scrapeIngredients(callback) {

    return rp('https://www.themealdb.com/api/json/v1/1/list.php?i=list')
        .then(res => {

            const ingredients = JSON.parse(res).meals.map(ingredient => {
                return {
                    id: ingredient.idIngredient,
                    name: ingredient.strIngredient
                }
            })

            fs.writeFile("./data/ingredients.json", JSON.stringify(ingredients), function(err) {
                if (err) return callback(err);
                callback(null)
            });

        })
}

module.exports = scrapeIngredients