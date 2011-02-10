<?php

defined('SYSPATH') or die('No direct script access.');

/* * *
 * Generate SQL Create statement from sprig model
 *
 * @package SprigGen
 * @author Alex Tucker
 * @copyright (c) Alex Tucker 2011
 * @liscence MIT
 */
Class Controller_Spriggen extends Controller {

    public function action_index() {
        // Dont run unless in dev mode
        if (Kohana::$environment == Kohana::DEVELOPMENT) {
            $modelName = $this->request->param('model');

            // Check if a model name is passed
            if ($modelName) {
                // try to find model
                try {
                    $model = Sprig::factory($modelName);
                    $fields = $model->fields();
                    $sql = sprintf('CREATE TABLE %ss (', $modelName);

                    // Loop threw the fields
                    foreach ($fields as $field => $atrib) {
                        $field_type = get_class($atrib);
                        switch ($field_type) {

                            case 'Sprig_Field_Auto':
                                $sql = $sql . $this->GenIDSql($field);
                                $pk = $this->GenPrimaryKey($field);
                                break;

                            case 'Sprig_Field_Char':
                                $sql = $sql . $this->GenVarchar($field, $atrib);
                                break;

                            case 'Sprig_Field_Email':
                                $sql = $sql . $this->GenEmail($field);
                                break;
                        }
                    }

                    // Add primary key and close statement
                    $sql = $sql . $pk . ');';
                    $this->request->response = $sql;
                } catch (ErrorException $e) {
                    // Model does not exist
                    // This is broken, not sure how to catch this exception
                    $this->request->response = 'Model does not exist';
                }
            } else {
                // No Model Name Passed
                $this->request->response = 'No Model Name Passed';
            }
        }
    }

    private function GenIDSql($fieldName) {
        return sprintf('%s integer', $fieldName);
    }

    private function GenVarchar($fieldName, $atrib, $buffer = 0, $utf8 = false) {
        if (!$atrib->max_length) {
            if ($utf8) {
                $max = 21844; // MySQL  varchar max for utf8
            } else {
                $max = 65535; // MySQL varchar max for non-utf8
            }
        } else {
            $max = $atrib->max_length + $buffer;
        }

        if ($utf8) {
            return sprintf(', %s varchar(%s) CHARACTER SET utf8', $fieldName, $max);
        } else {
            return sprintf(', %s varchar(%s)', $fieldName, $max);
        }
    }

    private function GenEmail($fieldName) {
        return sprintf(', %s varchar(%s)', $fieldName, '200');
    }

    private function  GenPrimaryKey($field) {
        return sprintf(', PRIMARY KEY(%s)', $field);
    }

}
