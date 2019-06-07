<?php

namespace Bid\Controllers;


use Bid\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends Controller
{

    public function edit(Request $request, Response $response, $args)
    {
        $user = User::findOrFail($args['id']);

        return $this->view->render($response, "administrator/users/user.twig",
            [
                'user' => $user,
                'title' => $user->full_name
            ]
        );
    }

    public function create(Request $request, Response $response)
    {
        return $this->view->render($response, "administrator/users/user.twig",
            [
                "title" => "Nuevo usuario"
            ]);
    }

    public function update(Request $request, Response $response, $args)
    {
        try {

            $update= $request->getParams();
            $change = $request->getParam('change_password');
            if (isset($change)) {
                $update['password'] = password_hash($update['new_password'], PASSWORD_DEFAULT);
            }
            $user = User::updateOrCreate([
                "id" => $args['id'], "username" => $update['username']
            ], $update);
            $this->flash->addMessage("creators", "Correcto: Usuario {$user->username} editado correctamente.");

            return $response->withRedirect($this->router->pathFor('panel.user.list'));
        } catch (QueryException $e) {
            $this->flash->addMessage("errors", "Error: " . $e->getMessage());
            return $response->withRedirect($this->router->pathFor('panel.user.edit', [ 'id' => $args['id']]));
        }
    }

    public function store(Request $request, Response $response)
    {
        try {

            $user = $request->getParams();
            $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
            User::create($user);
            $this->flash->addMessage("creators", "Correcto: usuario creado.");
            return $response->withRedirect($this->router->pathFor('panel.user.list'));
        } catch ( QueryException $e) {
            $this->flash->addMessage("errors", "Error:" . $e->getMessage());
            return $response->withRedirect($this->router->pathFor('panel.user.create'));
        }
    }

    public function delete( Request $request, Response $response, $args)
    {
        try {
            $user = User::findOrFail($args['id']);
            if ($user->delete()) {
                $this->flash->addMessage("creators", "Correcto: usuario eliminado correctamente");
                return $response->withRedirect($this->router->pathFor('panel.user.list'));
            }
        } catch (QueryException $e) {
            $this->flash->addMessage("errors", "Error: " . $e->getMessage());
            return $response->withRedirect($this->router->pathFor('panel.user.list'));
        } catch (ModelNotFoundException $e) {
            $this->flash->addMessage("errors", "Error: " . $e->getMessage());
            return $response->withRedirect($this->router->pathFor('panel.user.list'));
        }
    }

    public function home(Request $request, Response $response)
    {
        $users = User::all();

        return $this->view->render($response, "administrator/users/home.twig", [
            "users" => $users
        ]);
    }
}