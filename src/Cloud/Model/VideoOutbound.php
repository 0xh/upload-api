<?php

namespace Cloud\Model;

/**
 * @Entity
 */
class VideoOutbound extends AbstractModel
{
    const STATUS_PENDING = 'pending';
    const STATUS_WORKING = 'working';
    const STATUS_COMPLETE = 'complete';
    const STATUS_ERROR = 'error';

    //////////////////////////////////////////////////////////////////////////

    use Traits\IdTrait;

    /**
     * @JoinColumn(nullable=false)
     * @ManyToOne(targetEntity="Video", inversedBy="outbounds")
     */
    protected $video;
}
