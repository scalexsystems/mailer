<?php namespace Scalex\Mailer;

/**
 * @property int project_id
 * @property string data
 * @property string label
 * @property \Carbon\Carbon created_at
 */
class Track extends Eloquent\Model
{
    protected $fillable = ['label', 'data', 'project_id'];

    public function member() {
        return $this->belongsTo(Member::class);
    }

    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function __toString() {
        return $this->label.': '.$this->data.' (at '.$this->created_at->format($this->getDateFormat()).')';
    }
}
