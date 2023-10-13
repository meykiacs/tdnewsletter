<?php

namespace TdNewsletter\Entity\Model;

class User extends Entity {
  public \WP_User $wp_user;
  public bool $is_active;

  public function __construct(\WP_User $wp_User) {
    $this->wp_user = $wp_User;
    $this->id = $wp_User->ID;
  }

  public function getUserInfo() {
    return [
      'username'  =>  $this->wp_user->user_login,
      'email' => $this->wp_user->user_email,
      'firstName' =>  $this->wp_user->first_name,
      'lastName' =>  $this->wp_user->last_name,
      'isActive'  => $this->is_active,
      'subscribedPlans' => $this->getSubscribedPlans()
    ];
  }

  /**
   * Summary of getSubscribedPlans
   * @return Subscription[]
   */
  public function getSubscribedPlans(): array {
    // $sql = "SELECT * from subscription
    //         WHERE owner_id=$this->wp_user->ID";
    // global $wpdb;
    // $rows = $wpdb->get_results($sql);
    // var_dump($rows);
    return [];
  }

  public function addNewSubscription(Subscription $subscription): int {
    $sql = "INSERT INTO subscription (
      id, owner_id, plan_id, expiration_date,
      start_date, payment_status, payment_method,
      auto_renewal, status, last_payment_date,
      next_payment_date, created_at, updated_at
      )
      VALUES
      (
        DEFAULT, $this->wp_user->ID, $subscription->plan_id,
        $subscription->expiration_date
      ";
  }
}
