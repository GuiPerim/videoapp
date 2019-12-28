<?php

namespace App\Controller;

use App\Entity\Business;
use App\Entity\Category;
use App\Entity\Tasks;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BusinessController extends AbstractController
{
    /**
     * @Route("/admin/business", name="admin/list")
     */
    public function index()
    {
        $business = $this->getDoctrine()->getRepository(Business::class)->findBy([], ['id' => 'DESC']);
        return $this->render('business/index.html.twig', [
            'controller_name' => 'Teste',
            'business' => $business,
        ]);
    }

    /**
     * @Route("/admin/new", name="admin/new")
     */
    public function create()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('business/create.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/create", name="add_business", methods={"post"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createBusiness(Request $request)
    {
        $errors = null;

        //Recuperamos o valor enviado através do método POST
        $title = $request->request->get('title');
        $phone = $request->request->get('phone');
        $address = $request->request->get('address');
        $zipcode = $request->request->get('zipcode');
        $state = $request->request->get('state');
        $city = $request->request->get('city');
        $description = $request->request->get('description');
        $categories = $request->request->get('category');

        //Se o valor enviado for nulo, redirecionamos para á página inicial mostrando a msg de erro
        if (empty($title)) {
            $errors .= "<p>Enter a valid <strong>title</strong></p>";
        }
        if (empty($address)) {
            $errors .= "<p>Enter a valid <strong>address</strong></p>";
        }
        if (empty($zipcode)) {
            $errors .= "<p>Enter a valid <strong>zipcode</strong></p>";
        }
        if (empty($state)) {
            $errors .= "<p>Enter a valid <strong>state</strong></p>";
        }
        if (empty($city)) {
            $errors .= "<p>Enter a valid <strong>city</strong></p>";
        }
        if (empty($description)) {
            $errors .= "<p>Enter a valid <strong>description</strong></p>";
        }
        if (empty($categories)) {
            $errors .= "<p>Select at least one <strong>category</strong></p>";
        }

        if ($errors) {
            $this->addFlash(
                'warning',
                $errors
            );
        }
        else {
            try {
                //Criamos o gerenciador da entidade
                $entityManager = $this->getDoctrine()->getManager();

                //Criamos o objeto e setamos seus valores
                $business = new Business();
                $business->setName($title);
                $business->setPhone($phone);
                $business->setAddress($address);
                $business->setCep($zipcode);
                $business->setCity($city);
                $business->setState($state);
                $business->setDescription($description);

                foreach ($categories as $index => $catSelected) {
                    $cat = $this->getDoctrine()->getRepository(Category::class)->find($catSelected);
                    $business->addCategory($cat);
                }

                //Persistimos as informação na base de dados
                $entityManager->persist($business);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Business Create!!'
                );
            }
            catch(DBALException $e){
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
            catch(\Exception $e){
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
            }
        }

        //TODO: Ao redirecionar, enviar os parametros cadastrados para não precisar digitar novamente os valores
        return $this->redirectToRoute('admin/new');
    }
}
