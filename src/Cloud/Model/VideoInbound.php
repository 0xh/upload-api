<?php

namespace Cloud\Model;

/**
 * @Entity @Table(name="video_inbound")
 **/
class VideoInbound
{
    const STATUS_PENDING = 'pending';
    const STATUS_WORKING = 'working';
    const STATUS_COMPLETE = 'complete';
    const STATUS_ERROR = 'error';

    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;

    /** @ManyToOne(targetEntity="Video", inversedBy="videoInbounds") 
     * @JoinColumn(name="video_id", referencedColumnName="id") 
     */
    protected $video;

    /** @ManyToOne(targetEntity="Site", inversedBy="videoInbounds") 
     * @JoinColumn(name="site_id", referencedColumnName="id") 
     */
    protected $site;

    public function setStatus($status)
    {
        if (!in_array($status, [
            self::STATUS_PENDING,
            self::STATUS_WORKING,
            self::STATUS_COMPLETE,
            self::STATUS_ERROR,
        ])
        ) {
            throw new \InvalidArgumentException(
                "Invalid Status"
            );
        }
        $this->status = $status;
    }
}
