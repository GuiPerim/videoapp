<?php

namespace App\Controller;

use App\Entity\Business;
use App\Repository\BusinessRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/search", name="search_business", methods={"post"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function searchBusiness(Request $request)
    {
        $errors = null;

        //Recuperamos o valor enviado através do método POST
        $search = $request->request->get('search');

        //Se o valor enviado for nulo, redirecionamos para á página inicial mostrando a msg de erro
        if (empty($search)) {
            $errors .= "<p>Enter a valid <strong>search</strong></p>";
        }

        if ($errors) {
            $this->addFlash('warning', $errors);
        }
        else {
            $result = $this->getDoctrine()->getRepository(BusinessRepository::class)->

            $business = $this->getDoctrine()->getRepository(Business::class)->find(array(
                'name'       => $search
                , 'address'  => $search
                , 'cep'      => $search
                , 'city'     => $search
            ));

            if (!empty($business)) {
                print_r($business);
            }
            else {
                $this->addFlash(
                    'warning',
                    'Deu ruim!!'
                );
            }
        }

        //TODO: Ao redirecionar, enviar os parametros cadastrados para não precisar digitar novamente os valores
        return $this->redirectToRoute('index');
    }
}
