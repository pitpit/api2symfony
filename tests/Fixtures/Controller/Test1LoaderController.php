<?php

namespace Creads\Tests\Api2Symfony\Fixtures\Controller;

use Symfony\Component\HttpFoundation\Request;

class Test1LoaderController extends Controller
{
    /**
     * Get a collection of categories.
     *
     * @Route(
     *   "/categories",
     *   name="get_categories"
     * )
     * @Method({"GET"})
     *
     * @return Response
     */
    public function getCategoriesAction(Request $request)
    {

        if ('json' === $request->get('_format')) {

            return new Response(
'{
  "total_count": 3,
  "offset": 0,
  "limit": 10,
  "items": [
    {
        "gid": "d41d8cd98f00b204e9800998ecf8427e",
        "title": "Exécution graphique",
        "description": "Détourage photo, retouches photo, adaptation de fichiers..."
    },
    {
        "gid": "76ac958cd4d0cb824f60af31221ef547",
        "title": "Logo & Identité",
        "description": "Création logo, mascotte, papeterie..."
    },
    {
        "gid": "06b853345badce620588b4352b015e03",
        "title": "Supports de communication",
        "description": "Affiche, brochure, plaquette commerciale..."
    },
    {
        "gid": "101d3ac9c042f7da921c0d5deccd4297",
        "title": "Logo & Identité",
        "description": "Création logo, mascotte, papeterie..."
    },
    {
        "gid": "4ed04216d4bef53a65e534f95756a3cc",
        "title": "Web & Mobile",
        "description": "Design de site internet, e-commerce, intégration HTML..."
    },
    {
        "gid": "8bc35eb465603da393cdc20a13a8b2e4",
        "title": "Conception & rédaction",
        "description": "Création de nom, slogan, création de contenu..."
    },
    {
        "gid": "494639b39e49bbeaf4bed5b50f95ef71",
        "title": "Vidéo",
        "description": "Storyboard, vidéo de présentation..."
    }
  ]
}',
                200,
                array()
            );
        }

        throw new HttpException(
            403,
'{
  "error": {
    "code": 403,
    "message" : "You are not authorized to see categories"
  }
}',
            null,
            array()
        );

        //returns an exception if the api does not know how to handle the request
        throw new BadRequestHttpException("Don't know how to handle this request");

    }

    protected function getProtectedAction()
    {
      return '';
    }
}
