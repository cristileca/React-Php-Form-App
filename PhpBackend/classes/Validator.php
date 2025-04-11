<?php
class Validator
{
    public function __construct(private $data, private $files) {}

    public function validate()
    {
        $errors = [];

        if (empty($this->data['email']) || !filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Valid email required";
        }

        if (empty($this->data['name'])) {
            $errors['name'] = "Name required";
        }

        $consent = isset($this->data['consent']) && $this->data['consent'] == 'true';
        $hasImage = isset($this->files['image']) && $this->files['image']['size'] > 0;

        if ($hasImage && !$consent) {
            $errors['consent'] = "Consent required if uploading image";
        }

        return $errors;
    }
}
