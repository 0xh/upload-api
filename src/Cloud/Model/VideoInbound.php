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

namespace Cloud\Model;

use Cloud\Model\VideoFile\InboundVideoFile;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;

use Doctrine\ORM\Mapping as ORM;
use Cloud\Doctrine\Annotation as CX;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 */
class VideoInbound extends AbstractModel
{
    const STATUS_PENDING  = 'pending';
    const STATUS_WORKING  = 'working';
    const STATUS_COMPLETE = 'complete';
    const STATUS_ERROR    = 'error';

    //////////////////////////////////////////////////////////////////////////

    use Traits\IdTrait;
    use Traits\CreatedAtTrait;
    use Traits\UpdatedAtTrait;
    use Traits\CompanyTrait;

    /**
     * @ORM\JoinColumn(nullable=false)
     * @ORM\ManyToOne(targetEntity="Video", inversedBy="inbounds")
     * @JMS\Groups({"details.inbounds"})
     */
    protected $video;

    /**
     * @ORM\OneToOne(
     *   targetEntity="Cloud\Model\VideoFile\InboundVideoFile",
     *   mappedBy="inbound"
     * )
     * @JMS\Groups({"details.inbounds", "details.videos"})
     */
    protected $videoFile;

    /**
     * @see STATUS_PENDING
     * @see STATUS_WORKING
     * @see STATUS_COMPLETE
     * @see STATUS_ERROR
     *
     * @ORM\Column(type="string", length=16)
     * @JMS\Groups({"list", "details.inbounds", "details.videos"})
     */
    protected $status = self::STATUS_PENDING;

    /**
     * @ORM\Column(type="string", length=48)
     * @JMS\Groups({"details.inbounds"})
     */
    protected $token;

    /**
     * #Column(type="datetime", nullable=true)
     * @JMS\Groups({"details.inbounds"})
     */
    protected $expiresAt;

    /**
     * Constructor
     *
     * @param Video $video must be passed to create an inbound
     *
     */
    public function __construct(Video $video)
    {
        $generator = new UriSafeTokenGenerator();
        $this->setToken($generator->generateToken());

        $this->setVideo($video);
    }

    /**
     * Set the upload identification token
     *
     * @param  string $token
     * @return VideoInbound
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Get the upload identification token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the parent video
     *
     * @param  Video $video
     * @return VideoInbound
     */
    public function setVideo($video)
    {
        $this->setCompany($video->getCompany());
        $this->video = $video;
        return $this;
    }

    /**
     * Get the parent video
     *
     * @return Video
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set the videofile for this inbound
     *
     * @param  InboundVideoFile $videoFile
     * @return VideoInbound
     */
    public function setVideoFile(InboundVideoFile $videoFile)
    {
        $this->videoFile = $videoFile;
        return $this;
    }

    /**
     * Get the videofile for this inbound
     *
     * @return InboundVideoFile
     */
    public function getVideoFile()
    {
        return $this->videoFile;
    }

    /**
     * Set the parent company
     *
     * @param  Company $company
     * @return VideoInbound
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * Set the parent company
     *
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set the processing status
     *
     * @param  string $status
     * @return VideoInbound
     */
    public function setStatus($status)
    {
        if (!in_array($status, [
            self::STATUS_PENDING,
            self::STATUS_WORKING,
            self::STATUS_COMPLETE,
            self::STATUS_ERROR,
        ])) {
            throw new \InvalidArgumentException("Invalid status");
        }

        $this->status = $status;
        return $this;
    }

    /**
     * Get the processing status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * Get the AWS S3 storage prefix or directory name
     *
     * @return string
     */
    public function getTempStoragePath()
    {
        return sprintf('inbounds/%d/%d/%s',
            $this->getVideo()->getId(),
            $this->getId(),
            $this->getToken()
        );
    }
}
