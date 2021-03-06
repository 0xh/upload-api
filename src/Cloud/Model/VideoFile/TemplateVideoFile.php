<?php
/**
 * cloudxxx-api (http://www.cloud.xxx)
 *
 * Copyright (C) 2014 Really Useful Limited.
 * Proprietary code. Usage restrictions apply.
 *
 * @copyright  Copyright (C) 2014 Really Useful Limited
 * @license    Proprietary
 */

namespace Cloud\Model\VideoFile;

use Cloud\Model\Video;

use Doctrine\ORM\Mapping as ORM;
use Cloud\Doctrine\Annotation as CX;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 */
class TemplateVideoFile extends AbstractVideoFile
{
    /**
     * Constructor
     *
     * @param Video $video  parent video file
     */
    public function __construct(Video $video)
    {
        $this->setVideo($video);
    }
}
