<?php

namespace TodoList\Controller;

use TodoList\Model\User;
use TodoList\Model\UserTable;
use TodoList\Form\LoginForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;

class AuthController extends AbstractActionController
{
    private $table;

    // The constructor
    public function __construct(UserTable $table)
    {
        $this->table = $table;
    }


    public function loginAction()
    {
        //if already login, redirect to success page
//        if ($this->getAuthService()->hasIdentity()){
//            return $this->redirect()->toRoute('task');
//        }

        $form = new LoginForm();
        $form->get('submit')->setValue('Login');

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $user = new User();
        $form->setInputFilter($user->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return ['form' => $form];
        }

        $data = $form->getData();

        try {
            $row = $this->table->getUserByUsername($data['username']);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('user', ['action' => 'login']);
        }

        if ($row->password == $data['password']) {

            return $this->redirect()->toRoute('task', ['userid' => $row->id] );
        }

//        $authService->getAdapter()
//            ->setIdentity($data['username'])
//            ->setCredential($data['password']);
//
//        $result = $authService->authenticate();

      //  var_dump($result);


    }

   }