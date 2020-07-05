const fs = require('fs')
const rp = require('request-promise')
const mealdbIngredients = require('./data/mealdb/ingredients.json')

const meals = []
const firstMealID = 52764
const lastMealID = 52956
let failedRequests = 0


function scrapeMeals(callback) {


    for (let mealID = firstMealID; mealID <= lastMealID; mealID++) {
        rp(`https://www.themealdb.com/api/json/v1/1/lookup.php?i=${mealID}`)
            .then(res => {

             

                if (!!JSON.parse(res).meals) {
                    meals.push(JSON.parse(res).meals[0])
                      
                }


                let formatedMeals = meals.map(meal => {
                    return {
                        id: meal.idMeal,
                        name: meal.strMeal,
                        cat: meal.strCategory,
                        instructions: meal.strInstructions,
                        area: meal.strArea,
                        thumbnail: meal.strMealThumb,
                        video: meal.strYoutube,
                        source: meal.strSource,
                        dateModified: meal.dateModified,
                        ingredients: (function() {
                            var arr = []
                            for (var i = 1; i <= 20; i++) {
                                arr.push(meal["strIngredient" + i])
                            }
                            return arr.filter(ing => ing !== "")
                        })()
                    }
                })

                fs.writeFile("./data/meals.json", JSON.stringify(formatedMeals), function(err) {
                    if (err) return console.log(err)
                    callback(null, formatedMeals)
                })

              
            })
            .catch((err) => {
                failedRequests++
                const error = new Error("Unable to get meal " + mealID)
                callback(err)
            })
    }

}

module.exports = scrapeMeals