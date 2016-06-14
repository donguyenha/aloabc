<?php
/**
 * Created by PhpStorm.
 * User: giaule
 * Date: 5/22/16
 * Time: 3:52 PM
 */
date_default_timezone_set('asia/ho_chi_minh');
$m = new \MongoClient();
$collection = $m->selectCollection('alofilm', 'films_crawler');
$collectionCategories = $m->selectCollection('alofilm', 'categories');
$collectionCategories->drop();

$collectionCountries = $m->selectCollection('alofilm', 'countries');
$collectionCountries->drop();

$collectionDirectors = $m->selectCollection('alofilm', 'directors');
$collectionDirectors->drop();

$collectionActors = $m->selectCollection('alofilm', 'actors');
$collectionActors->drop();

$collectionFilms = $m->selectCollection('alofilm', 'films_crawler_final');
$collectionFilms->drop();

$collectionTags = $m->selectCollection('alofilm', 'tags');
$collectionTags->drop();

/**
 * Lưu thông tin category
 */
$cursor = $collection->find();
$dataCategories = array();
$dataCountries = array();
$dataDirectors = array();
$dataActors = array();
$dataTags = array();

foreach ($cursor as $doc) {
    if (isset($doc['categories'])) {
        $categories = $doc['categories'];
        foreach ($categories as $category) {
            echo $category , "\n";
            $dataCategories[trim($category)] = trim($category);
        }
    }

    if (isset($doc['countries'])) {
        $countries = $doc['countries'];
        foreach ($countries as $country) {
            echo $country , "\n";
            $dataCountries[trim($country)] = trim($country);
        }
    }

    if (isset($doc['directors'])) {
        $directors = explode(',', $doc['directors']);
        foreach ($directors as $director) {
            echo $director , "\n";
            $dataDirectors[trim($director)] = trim($director);
        }
    }

    if (isset($doc['actors'])) {
        $actors = $doc['actors'];
        foreach ($actors as $actor) {
            $dataActors[] = array(
                'actor_name' => trim($actor['actor_name']),
                'cover' => $actor['cover'],
            );
        }
    }

    if (isset($doc['tags'])) {
        $tags = $doc['tags'];
        foreach ($tags as $tag) {
            echo $tag , "\n";
            $dataTags[trim($tag)] = trim($tag);
        }
    }
}

foreach ($dataCategories as $category) {
    $collectionCategories->insert(array(
        'category_name' => $category,
        'process_status' => 'public',
        'created_date'   => date('Y-m-d H:i:s'),
        'modified_date'   => date('Y-m-d H:i:s'),
    ));
}

foreach ($dataCountries as $country) {
    $collectionCountries->insert(array(
        'country_name' => $country,
        'process_status' => 'public',
        'created_date'   => date('Y-m-d H:i:s'),
        'modified_date'   => date('Y-m-d H:i:s'),
    ));
}

foreach ($dataDirectors as $director) {
    $collectionDirectors->insert(array(
        'director_name' => $director,
        'process_status' => 'public',
        'created_date'   => date('Y-m-d H:i:s'),
        'modified_date'   => date('Y-m-d H:i:s'),
    ));
}

foreach ($dataActors as $actor) {
    $collectionActors->insert(array(
        'actor_name' => $actor['actor_name'],
        'cover' => $actor['cover'],
        'process_status' => 'public',
        'created_date'   => date('Y-m-d H:i:s'),
        'modified_date'   => date('Y-m-d H:i:s'),
    ));
}

foreach ($dataTags as $tag) {
    $collectionTags->insert(array(
        'tag_name' => $tag,
        'process_status' => 'public',
        'created_date'   => date('Y-m-d H:i:s'),
        'modified_date'   => date('Y-m-d H:i:s'),
    ));
}

$cursor = $collection->find();

$dateCategories = array();
$i = 1;
$k = 0;
foreach ($cursor as $doc) {
    if (!isset($doc['film_name_vi'])) {
        $k ++;
    }
    $data = array(
        'crawler_link' => trim ($doc['crawler_link']),
        'crawler_film_id' => (int) $doc['crawler_film_id'],
        'crawler_source' => 'hdonline.vn',
        'film_name_vi' => trim ($doc['film_name_vi']),
        'film_name_en' => trim ($doc['film_name_en']),
        'cover' => trim ($doc['poster']),
        'poster' => trim ($doc['cover']),
        'year_released' => (int) $doc['year_released'],
        'film_type' => $doc['film_type'],
        'trailer' => trim($doc['trailer']),
        'description' => $doc['description'],

        'duration' => $doc['duration'],

        'subtitle_types' => $doc['subtitle_types'],
        'quality' => $doc['quality'],
        'rating_number' => $doc['rating_number'],

        'view_count' => 0,
        'download_count' => 0,
        'like_count' => 0,
        'files' => $doc['file']
    );

    if (isset($doc['categories'])) {
        $items = $doc['categories'];
        foreach ($items as $item) {
            $item = trim($item);

            $itemObject = $collectionCategories->findOne(array('category_name' => $item));
            $data['categories'][] = array(
                'category_id' => $itemObject['_id'],
                'category_name' => $itemObject['category_name']
            );
        }
    }

    if (isset($doc['countries'])) {
        $items = $doc['countries'];
        foreach ($items as $item) {
            $item = trim($item);

            $itemObject = $collectionCountries->findOne(array('country_name' => $item));
            $data['countries'][] = array(
                'country_id' => $itemObject['_id'],
                'country_name' => $itemObject['country_name']
            );
        }
    }

    if (isset($doc['directors'])) {
        $items = explode(',', $doc['directors']);
        foreach ($items as $item) {
            $item = trim($item);

            $itemObject = $collectionDirectors->findOne(array('director_name' => $item));
            $data['directors'][] = array(
                'director_id' => $itemObject['_id'],
                'director_name' => $itemObject['director_name']
            );
        }
    }

    if (isset($doc['actors'])) {
        $items = $doc['actors'];
        foreach ($items as $item) {
            $itemObject = $collectionActors->findOne(array('actor_name' => $item['actor_name']));

            $data['actors'][] = array(
                'actor_id' => $itemObject['_id'],
                'actor_name' => trim($itemObject['actor_name']),
                'cover' => trim($itemObject['cover'])
            );
        }
    }

    if (isset($doc['tags'])) {
        $items = $doc['tags'];
        foreach ($items as $item) {
            $item = trim($item);

            $itemObject = $collectionTags->findOne(array('tag_name' => $item));
            $data['tags'][] = array(
                'tag_id' => $itemObject['_id'],
                'tag_name' => $itemObject['tag_name']
            );
        }
    }

    $data['process_status'] = 'public';
    $data['created_date']   = date('Y-m-d H:i:s');
    $data['modified_date']  = date('Y-m-d H:i:s');

    $collectionFilms->insert($data);
    echo $i++ . "\n";
}
echo $k;