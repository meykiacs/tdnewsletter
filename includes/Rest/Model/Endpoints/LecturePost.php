<?php
declare(strict_types=1);
namespace TdNewsletter\Rest\Model\Endpoints;
use TdNewsletter\Rest\Model\Fields\NonEmptyTextField;


class LecturePost extends Endpoint
{

  public function __construct()
  {
    parent::__construct('POST');
    $this->addField(new NonEmptyTextField('class_name', true))->addField(new NonEmptyTextField('course_name', true))->addField(new NonEmptyTextField('num_of_stu', true))->addField(new NonEmptyTextField('entry', true))->addField(new NonEmptyTextField('date', true));
  }

  public function getPermissionCallback(): callable
  {
    // return '__return_true';
    return function (\WP_REST_Request $request): bool {
      return is_user_logged_in();

    };
  }

  public function getCallback(): callable
  {
    /**
     * @return \WP_REST_Response|\WP_Error
     */
    return function (\WP_REST_Request $request) {
      return new \WP_REST_Response(
        [
        ],
        201
      );
    };
  }

}