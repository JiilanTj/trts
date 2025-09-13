<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFollow extends Model
{
    protected $fillable = [
        'follower_id',
        'following_id',
        'followed_at',
    ];

    protected $casts = [
        'followed_at' => 'datetime',
    ];

    /**
     * Relationship to the user who is following (follower)
     */
    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    /**
     * Relationship to the user being followed (following)
     */
    public function following()
    {
        return $this->belongsTo(User::class, 'following_id');
    }

    /**
     * Follow a user (seller)
     */
    public static function followUser($followerId, $followingId)
    {
        // Pastikan tidak follow diri sendiri
        if ($followerId == $followingId) {
            return false;
        }

        // Pastikan yang di-follow adalah seller
        $userToFollow = User::find($followingId);
        if (!$userToFollow || !$userToFollow->isSeller()) {
            return false;
        }

        // Check jika sudah follow
        $existingFollow = self::where('follower_id', $followerId)
            ->where('following_id', $followingId)
            ->first();

        if ($existingFollow) {
            return false; // Sudah follow
        }

        // Create follow relationship
        $follow = self::create([
            'follower_id' => $followerId,
            'following_id' => $followingId,
            'followed_at' => now(),
        ]);

        // Update followers count di users table
        User::where('id', $followingId)->increment('followers');

        return $follow;
    }

    /**
     * Unfollow a user
     */
    public static function unfollowUser($followerId, $followingId)
    {
        $follow = self::where('follower_id', $followerId)
            ->where('following_id', $followingId)
            ->first();

        if ($follow) {
            $follow->delete();
            
            // Decrement followers count di users table
            User::where('id', $followingId)->decrement('followers');
            
            return true;
        }

        return false;
    }

    /**
     * Check if user is following another user
     */
    public static function isFollowing($followerId, $followingId)
    {
        return self::where('follower_id', $followerId)
            ->where('following_id', $followingId)
            ->exists();
    }

    /**
     * Get followers for a user
     */
    public static function getFollowers($userId, $limit = null)
    {
        $query = self::where('following_id', $userId)
            ->with('follower')
            ->orderBy('followed_at', 'desc');
            
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->get();
    }

    /**
     * Get following list for a user
     */
    public static function getFollowing($userId, $limit = null)
    {
        $query = self::where('follower_id', $userId)
            ->with('following')
            ->orderBy('followed_at', 'desc');
            
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->get();
    }
}
