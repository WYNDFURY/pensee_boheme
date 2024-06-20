<?php

namespace App\Orchid\Screens\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class UserScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'users' => User::latest()->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Liste des utilisateurs';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make(__('Create User'))
                ->icon('plus')
                ->modal('userModal')
                ->method('create'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('users', [
                TD::make('name'),
                TD::make('email'),
                TD::make('role'),

                TD::make('actions')
                    ->alignRight()
                    ->render(function (User $user) {
                        return Button::make('Delete User')
                            ->confirm('Are you sure you want to delete this user?')
                            ->method('delete', ['user' => $user->id]);
                    })
            ]),

            Layout::modal(
                'userModal',
                Layout::rows([
                    Input::make('user.name')
                        ->title('Name')
                        ->placeholder('Enter your name')
                        ->required(),
                    Input::make('user.email')
                        ->title('Email')
                        ->placeholder('Enter your email')
                        ->required(),
                    Input::make('user.password')
                        ->title('Password')
                        ->placeholder('Enter your password')
                        ->required(),
                    Input::make('user.password_confirmation')
                        ->title('Password Confirmation')
                        ->placeholder('Enter your password confirmation')
                        ->required(),
                ])
            )
                ->title('Create User')
                ->applyButton('Create'),
        ];
    }

    public function create(Request $request)
    {
        // Handle the creation of a new user
        $request->validate([
            'user.name' => 'required|string',
            'user.email' => 'required|email',
            'user.password' => 'required|string|confirmed',
        ]);

        $user = new User();
        $user->name = $request->input('user.name');
        $user->email = $request->input('user.email');
        $user->password = Hash::make($request->input('user.password'));
        $user->save();
    }

    public function delete(User $user)
    {
        // Handle the deletion of a user
        $user->delete();
    }
}
