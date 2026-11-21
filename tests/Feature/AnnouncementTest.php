<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    public function test_school_officer_can_publish_an_announcement(): void
    {
        $this->artisan('migrate', [
            '--path' => [
                'database/migrations/0001_01_01_000000_create_users_table.php',
                'database/migrations/2026_07_16_023136_create_roles_table.php',
                'database/migrations/2026_07_16_023140_create_permissions_table.php',
                'database/migrations/2026_07_16_023145_create_role_user_table.php',
                'database/migrations/2026_07_16_023150_create_permission_role_table.php',
                'database/migrations/2026_07_16_023319_create_conversations_table.php',
                'database/migrations/2026_07_16_023323_create_conversation_participants_table.php',
                'database/migrations/2026_07_16_023327_create_messages_table.php',
                'database/migrations/2026_07_16_023347_create_announcements_table.php',
                'database/migrations/2026_07_16_023352_create_notifications_table.php',
                'database/migrations/2026_11_21_000000_add_category_to_announcements_table.php',
            ],
        ])->assertSuccessful();

        $this->withoutVite();

        $officer = User::factory()->create();
        $role = Role::create(['name' => 'school_officer', 'display_name' => 'School Officer']);
        $officer->roles()->attach($role);
        $this->actingAs($officer);

        $this->get(route('announcements.create'))
            ->assertOk()
            ->assertSee('Publish Announcement');

        $response = $this->post(route('announcements.store'), [
            'title' => 'Campus Closure Notice',
            'content' => 'The campus will be closed tomorrow.',
            'category' => 'urgent',
            'audience' => 'students',
            'is_pinned' => '1',
        ]);

        $response->assertSessionHas('success');
        $announcement = Announcement::sole();
        $this->assertSame($officer->id, $announcement->created_by);
        $this->assertSame('urgent', $announcement->category);
        $this->assertTrue($announcement->is_pinned);
        $response->assertRedirect(route('announcements.show', $announcement));

        $this->get(route('announcements.show', $announcement))
            ->assertOk()
            ->assertSee('Campus Closure Notice')
            ->assertSee('The campus will be closed tomorrow.');

        $this->get(route('announcements.index'))
            ->assertOk()
            ->assertSee('Campus Closure Notice');
    }
}
