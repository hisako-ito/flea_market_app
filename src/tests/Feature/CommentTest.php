<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Validator;

class CommentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    /**
     * カスタムリクエストのバリデーションテスト
     *
     * @param array $keys 項目名の配列
     * @param array $values 値の配列
     * @param bool $expect 期待値 (true: バリデーションOK、false: バリデーションNG)
     * @param array $expectedErrors 期待するエラーメッセージの配列
     * @dataProvider dataUserComment
     */

    public function testCommentRequest(array $keys, array $values, bool $expect, array $expectedErrors = [])
    {
        $dataList = array_combine($keys, $values);
        $request = new CommentRequest();
        $rules = $request->rules();
        $messages = $request->messages();
        $validator = Validator::make($dataList, $rules, $messages);
        $result = $validator->passes();
        $this->assertEquals($expect, $result);

        if (!$result) {
            $errors = $validator->errors()->toArray();
            foreach ($expectedErrors as $field => $expectedMessage) {
                $this->assertArrayHasKey($field, $errors);
                $this->assertContains($expectedMessage, $errors[$field]);
            }
        }
    }

    public function dataUserComment()
    {
        return [
            [['content'], ['これはコメントです'], true],

            [['content'], [''], false, ['content' => 'コメントを入力してください']],
        ];
    }

    public function testLoginUserCommentAdd()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('item.comments.store', ['item_id' => $item->id]), [
                'content' => 'これはテストコメントです',
            ]);

        $response->assertStatus(302);

        $response->assertRedirect(route('item.detail', ['item_id' => $item->id]));

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'これはテストコメントです',
        ]);
    }

    public function testNotLoginUserCommentAdd()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */
        $item = Item::factory()->create();

        $response = $this->post(route('item.comments.store', ['item_id' => $item->id]), [
            'content' => 'これはテストコメントです',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseMissing('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'これはテストコメントです',
        ]);
    }

    public function testCommentExceedsMaxLength()
    {
        $user = User::factory()->create();
        /** @var \App\Models\User $user */
        $item = Item::factory()->create();

        $longComment = str_repeat('あ', 256);

        $response = $this->actingAs($user)
            ->post(route('item.comments.store', ['item_id' => $item->id]), [
                'content' => $longComment,
            ]);

        $response->assertStatus(302);

        $response->assertSessionHasErrors([
            'content' => 'コメントは255文字以内で入力してください',
        ]);

        $this->assertDatabaseMissing('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => $longComment,
        ]);
    }
}
