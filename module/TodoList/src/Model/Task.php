<?php
namespace TodoList\Model;

use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;

class Task
{
    public $id;
    public $userid;
    public $title;
    public $status;

    private $inputFilter;

    public function exchangeArray(array $data)
    {
        $this->id = !empty($data['id']) ? $data['id'] : null;
        $this->userid = !empty($data['userid']) ? $data['userid'] : null;
        $this->title = !empty($data['title']) ? $data['title'] : null;
        $this->status = !empty($data['status']) ? $data['status'] : null;
    }

    public function getArrayCopy()
    {
        return [
            'id'     => $this->id,
            'userid' => $this->userid,
            'title'  => $this->title,
            'status' => $this->status,
        ];
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new DomainException(sprintf(
            '%s does not allow injection of an alternate input filter',
            __CLASS__
        ));
    }

    public function getInputFilter()
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'title',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'status',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ]
        ]);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }
}