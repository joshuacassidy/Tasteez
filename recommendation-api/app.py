from flask import Flask, Response, request,render_template
import json
app = Flask(__name__)
import math

@app.route('/', methods=['POST'])
def recommendRecipes():
    try:
        likeData = json.loads(request.data)["liked"]
    except:
        return Response(json.dumps({'message': "Like data is a required to make recommendations!"}), mimetype='application/json', status='400')
    
    try:
        mealData = json.loads(request.data)["meals"]
    except:
        return Response(json.dumps({'message': "Recipe data is a required to make recommendations!"}), mimetype='application/json', status='400')
    
    likes = [i['name'] for i in likeData if i["like_value"] == "1"]
    dislikes = [i['name'] for i in likeData if i["like_value"] == "-1"]
    uniqueLikes = [i for i in likes if i not in dislikes] 
    uniqueDislikes = [i for i in dislikes if i not in likes] 

    uniqueIngredientsByName = uniqueLikes + uniqueDislikes

    uniqueIngredients = [i for i in likeData if i["name"] in uniqueIngredientsByName]
    
    for i in uniqueIngredients:
        if int(i['like_value']) == 1:
            i['like_value'] = 3
    
    ingredientsScores = {}
    
    for i in uniqueIngredients:
        if i['name'] in ingredientsScores:
            ingredientsScores[i['name']]['likes']+= int(i["like_value"])
        else:
            ingredientsScores[i['name']] = {'name': i['name'], 'likes': int(i["like_value"])}

    ingredientsScoresFlattened = [i for i in ingredientsScores.values()]
    
    for i in ingredientsScoresFlattened:
        if i['likes'] < 0:
            i['likes'] = 1

    for meal in mealData:
        ingredients = meal['ingredient']
        itm = {i['name']:i for i in ingredientsScoresFlattened if i['name'] in ingredients}
        iSum = 0
        for i in ingredients:
            if i in itm:
                iSum+= (itm[i]['likes'] - 1) ** 2
            else:
                iSum += 1
        meal['weight'] = math.sqrt(iSum)
    

    return Response(json.dumps(
        sorted(mealData, key=lambda k: k['weight'], reverse=True)[:max(int(len(mealData)/4), 20)]
        ), mimetype='application/json', status='200')

if __name__ == '__main__':
    app.run(debug=True)