<?php

namespace App\Services;

use App\Models\Note;
use App\Models\User;

class NoteService
{
    /**
     * Get all notes for a user.
     *
     * @param User $user
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserNotes(User $user, array $filters = [])
    {
        $query = $user->notes();

        // Filter by category
        if (isset($filters['category_id'])) {
            $query->byCategory($filters['category_id']);
        }

        // Filter by pinned status
        if (isset($filters['is_pinned'])) {
            $query->where('is_pinned', $filters['is_pinned']);
        }

        // Search by title or content
        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        // Order by pinned first, then by created date
        $query->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc');

        return $query->get();
    }

    /**
     * Create a new note.
     *
     * @param User $user
     * @param array $data
     * @return Note
     */
    public function createNote(User $user, array $data): Note
    {
        $noteData = [
            'user_id' => $user->id,
            'title' => $data['title'],
            'content' => $data['content'],
            'category_id' => $data['category_id'] ?? null,
            'is_pinned' => $data['is_pinned'] ?? false,
        ];

        return Note::create($noteData);
    }

    /**
     * Get a specific note for a user.
     *
     * @param User $user
     * @param int $noteId
     * @return Note|null
     */
    public function getNote(User $user, int $noteId): ?Note
    {
        return $user->notes()->find($noteId);
    }

    /**
     * Update a note.
     *
     * @param Note $note
     * @param array $data
     * @return Note
     */
    public function updateNote(Note $note, array $data): Note
    {
        $updateData = [];

        if (isset($data['title'])) {
            $updateData['title'] = $data['title'];
        }

        if (isset($data['content'])) {
            $updateData['content'] = $data['content'];
        }

        if (isset($data['category_id'])) {
            $updateData['category_id'] = $data['category_id'];
        }

        if (isset($data['is_pinned'])) {
            $updateData['is_pinned'] = $data['is_pinned'];
        }

        $note->update($updateData);
        $note->refresh();

        return $note;
    }

    /**
     * Delete a note.
     *
     * @param Note $note
     * @return bool
     */
    public function deleteNote(Note $note): bool
    {
        return $note->delete();
    }

    /**
     * Toggle pin status of a note.
     *
     * @param Note $note
     * @return Note
     */
    public function togglePin(Note $note): Note
    {
        $note->update(['is_pinned' => !$note->is_pinned]);
        $note->refresh();

        return $note;
    }

    /**
     * Bulk create notes.
     *
     * @param User $user
     * @param array $notesData
     * @return array
     */
    public function bulkCreateNotes(User $user, array $notesData): array
    {
        $createdNotes = [];
        $errors = [];

        foreach ($notesData as $index => $noteData) {
            try {
                $data = [
                    'user_id' => $user->id,
                    'title' => $noteData['title'] ?? null,
                    'content' => $noteData['content'],
                    'category_id' => $noteData['category_id'] ?? null,
                    'is_pinned' => $noteData['is_pinned'] ?? false,
                ];

                // Create note without timestamps first
                $note = new Note($data);

                // Set custom timestamps if provided
                if (isset($noteData['created_at'])) {
                    $note->created_at = $noteData['created_at'];
                }
                if (isset($noteData['updated_at'])) {
                    $note->updated_at = $noteData['updated_at'];
                }

                // Save without updating timestamps
                $note->timestamps = false;
                $note->save();
                $note->timestamps = true;

                $createdNotes[] = $note;
            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'created' => $createdNotes,
            'errors' => $errors,
        ];
    }

    /**
     * Bulk update notes.
     *
     * @param User $user
     * @param array $notesData
     * @return array
     */
public function bulkUpdateNotes(User $user, array $notesData): array
    {
        $updatedNotes = [];
        $errors = [];

        foreach ($notesData as $index => $noteData) {
            try {
                $note = $user->notes()->find($noteData['id']);

                if (!$note) {
                    $errors[] = [
                        'index' => $index,
                        'id' => $noteData['id'],
                        'error' => 'Note not found or does not belong to user',
                    ];
                    continue;
                }

                $updateData = [];


                
                if (array_key_exists('title', $noteData)) {
                    $updateData['title'] = $noteData['title'];
                }


                if (array_key_exists('content', $noteData)) {
                    $updateData['content'] = $noteData['content'];
                }

                if (array_key_exists('category_id', $noteData)) {
                    $updateData['category_id'] = $noteData['category_id'];
                }

                if (array_key_exists('is_pinned', $noteData)) {
                    $updateData['is_pinned'] = $noteData['is_pinned'];
                }

                // Handle custom timestamps
                $customTimestamps = false;

                if (array_key_exists('created_at', $noteData)) {
                    $updateData['created_at'] = $noteData['created_at'];
                    $customTimestamps = true;
                }

                if (array_key_exists('updated_at', $noteData)) {
                    $updateData['updated_at'] = $noteData['updated_at'];
                    $customTimestamps = true;
                }

                // Disable automatic timestamps if custom ones provided
                if ($customTimestamps) {
                    $note->timestamps = false;
                }

                $note->update($updateData);

                if ($customTimestamps) {
                    $note->timestamps = true;
                }

                $note->refresh();
                $updatedNotes[] = $note;
            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'id' => $noteData['id'] ?? null,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'updated' => $updatedNotes,
            'errors' => $errors,
        ];
    }

    /**
     * Bulk delete notes.
     *
     * @param User $user
     * @param array $noteIds
     * @return array
     */
    public function bulkDeleteNotes(User $user, array $noteIds): array
    {
        $deleted = [];
        $errors = [];

        foreach ($noteIds as $index => $noteId) {
            try {
                $note = $user->notes()->find($noteId);

                if (!$note) {
                    $errors[] = [
                        'index' => $index,
                        'id' => $noteId,
                        'error' => 'Note not found or does not belong to user',
                    ];
                    continue;
                }

                $note->delete();
                $deleted[] = $noteId;
            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'id' => $noteId,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'deleted' => $deleted,
            'errors' => $errors,
        ];
    }
}
