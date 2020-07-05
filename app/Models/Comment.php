<?php

namespace Tasteez\Models;

use DateTime;

class Comment extends Model {

  public function getAll($mealID, $userID) {

    $query = "SELECT
      comments.comment_id, comments.comment, comments.date_added, users.username, comments.user_id
      FROM comments
      INNER JOIN users
      ON users.id = comments.user_id
      WHERE comments.recipe_id = '${mealID}' ORDER BY comments.date_added DESC";

    $comments = $this->db->query($query)->fetchAll();
    $comment["usersComment"] = false;
    $today = date('Y/m/d H:i:s');
    $todaysDate = new DateTime($today);

    foreach ($comments as &$comment) {

      if ($comment["user_id"] == $userID) {
        $comment["usersComment"] = true;
      }

      $commentDate = new DateTime($comment["date_added"]);
      $currentDate = new DateTime(date('Y/m/d H:i:s'));
      $interval = $commentDate->diff($currentDate);
      $shortDate = "";
      $yearDiff = $interval->format('%y');
      $monthDiff = $interval->format('%m');
      $dayDiff = $interval->format('%d');
      $hourDiff = $interval->format('%h');
      $minDiff = $interval->format('%i');

      if ($yearDiff > 0) {
        $shortDate = $yearDiff == 1 ? "1 Year ago" : $yearDiff . " years ago";
      } else if ($monthDiff > 0) {
        $shortDate = $monthDiff == 1 ? "1 Month ago" : $monthDiff . " Months ago";
      } else if ($dayDiff > 0) {
        $shortDate = $dayDiff == 1 ? "Yesterday" : $dayDiff . " Days ago";
      } else if ($hourDiff > 0) {
        $shortDate = $hourDiff == 1 ? "1 hour ago" : $hourDiff . " hours ago";
      } else {
        $shortDate = $minDiff == 1 ? "1 minnute ago" : $minDiff . " minutes ago";
      }

      $comment["shortDate"] = $shortDate;
      $comments = $comments;
    }

    return $comments;
  }

  public function addComment($comment, $mealID, $userID) {
    $dateString = date('Y/m/d H:i:s');

    $stmt = $this->db->prepare("INSERT
      INTO comments (comment, date_added, user_id, recipe_id)
      VALUES (:comment, '${dateString}', :user_id, :meal_id )");
    $comment = preg_replace('/[\;\(\)\<\>\/\*]/', ' ', $comment);  
    $stmt->execute([
      ":comment" => $comment,
      ":user_id" => $userID,
      ":meal_id" => $mealID
    ]);

    return $stmt;
  }

  public function deleteComment($commentID, $userID) {
    $stmt = $this->db->prepare("DELETE
      FROM comments
      WHERE comment_id = :comment_id AND user_id = :user_id");

    $stmt->execute([ ":user_id" => $userID, ":comment_id" => $commentID]);

    return $stmt;
  }

}
