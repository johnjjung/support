<?php

/**
 * @package     Support
 * @author      Ian Olson <me@ianolson.io>
 * @license     MIT
 * @copyright   2015, Ian Olson
 */

namespace IanOlson\Support\Traits;

use IanOlson\Support\Models\Meta;
use Artesaos\SEOTools\Traits\SEOTools;

trait SeoTrait
{
    use SEOTools;

    /**
     * SEO Keys used on the application.
     *
     * @var array
     */
    protected $keys = [
      'setTitle',
      'setDescription',
      'opengraph:setTitle',
      'opengraph:setSiteName',
      'opengraph:setUrl',
      'opengraph:setDescription',
      'opengraph:addImage',
      'twitter:setType',
      'twitter:setTitle',
      'twitter:setSite',
      'twitter:setUrl',
      'twitter:setDescription',
      'twitter:addImage',
    ];

    /**
     * SEO Keys that are images.
     *
     * @var array
     */
    protected $keyImages = [
      'opengraph:addImage',
      'twitter:addImage',
    ];

    /**
     * Setup default SEO values for views.
     *
     * @param array $metaData
     */
    public function setupSeoValues(array &$metaData)
    {
        foreach ($this->keys as $key) {
            if (!array_key_exists($key, $metaData)) {
                $metaData[$key] = null;
            }
        }
    }

    /**
     * Strip out SEO values from data.
     *
     * @param array $data
     *
     * @return array
     */
    public function stripSeoValues(array &$data)
    {
        $metaData = [];

        foreach ($this->keys as $key) {
            if (array_key_exists($key, $data)) {
                $metaData[$key] = $data[$key];
                unset($data[$key]);
            }
        }

        return $metaData;
    }

    /**
     * Update meta data.
     *
     * @param $model
     * @param $id
     * @param $method
     * @param $value
     *
     * @return mixed
     */
    public function updateMetaData($model, $id, $method, $value)
    {
        if (in_array($method, $this->keyImages)) {
            $path  = $this->uploadImage($value);
            $value = $path['full'];
        }

        $class = get_class($model);

        $meta = Meta::where('method', $method)
                    ->where('reference_id', $id)
                    ->where('reference_type', $class)
                    ->first();

        if (empty($meta)) {
            $metaData = $model->meta()
                              ->create(['method' => $method, 'value' => $value]);

            return $metaData;
        }

        $meta->value = $value;

        $metaData = $meta->save();

        return $metaData;
    }

    /**
     * Setup Meta Data
     *
     * @param array $data
     */
    public function setUp(array $data)
    {
        if (empty($data)) {
            return;
        }

        foreach ($data as $m) {

            // Setup open graph tags.
            if (strpos($m['method'], 'opengraph:') !== false) {
                $this->openGraph($m['value'], $m['method']);
                continue;
            }

            // Setup twitter tags.
            if (strpos($m['method'], 'twitter:') !== false) {
                $this->twitterCard($m['value'], $m['method']);
                continue;
            }

            // Setup default tags.
            $this->defaultTags($m['value'], $m['method']);
        }
    }

    /**
     * Setup meta data from settings table.
     *
     * @param array  $data
     * @param array  $keys
     * @param string $prefix
     */
    public function setUpSettingMetaData(array &$data, array $keys, $prefix)
    {
        if (empty($data)) {
            return;
        }

        foreach ($data as $method => $value) {

            // Setup $index for future use.
            $index = $method;

            // Remove prefix from the setting name.
            $method = str_replace($prefix, '', $method);

            // If the $method name does not exist in the $keys array, continue.
            if (!in_array($method, $keys)) {
                continue;
            }

            // Unset the index after the meta tag has been found.
            unset($data[$index]);

            // Setup open graph tags.
            if (strpos($method, 'opengraph:') !== false) {
                $this->openGraph($value, $method);
                continue;
            }

            // Setup twitter tags.
            if (strpos($method, 'twitter:') !== false) {
                $this->twitterCard($value, $method);
                continue;
            }

            // Setup default tags.
            $this->defaultTags($value, $method);

        }

        return;
    }

    /**
     * Setup default meta data.
     *
     * @param string $value
     * @param string $method
     *
     * @method setTitle
     * @method setDescription
     */
    protected function defaultTags($value, $method)
    {
        $this->seo()
             ->$method($value);
    }

    /**
     * Setup open graph meta data.
     *
     * @param string $value
     * @param string $method
     *
     * @method addImage
     * @method setTitle
     * @method setDescription
     * @method setUrl
     * @method setSiteName
     */
    protected function openGraph($value, $method)
    {
        $type   = explode(':', $method);
        $method = $type[1];
        $this->seo()
             ->opengraph()
             ->$method($value);
    }

    /**
     * Setup twitter card meta data.
     *
     * @param string $value
     * @param string $method
     *
     * @method setType
     * @method setTitle
     * @method setSite
     * @method setDescription
     * @method setUrl
     * @method addImage
     */
    protected function twitterCard($value, $method)
    {
        $type   = explode(':', $method);
        $method = $type[1];
        $this->seo()
             ->twitter()
             ->$method($value);
    }

    /**
     * Meta data for model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function meta()
    {
        return $this->morphMany('IanOlson\Support\Models\Meta', 'reference');
    }
}