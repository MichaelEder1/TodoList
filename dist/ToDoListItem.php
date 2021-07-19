<?php

class ToDoListItem
{
    private string $todoId;
    private DateTime $createdOn;
    private string $userId;
    private string $todoTitle;
    private string $todoText;
    private DateTime $lastEdit;
    private string $userIdOfLastEdit;
    private bool $status; //true = open; false = closed


    public function __construct(string $taskId, string $userId, string $taskTitle, $taskStatus, DateTime $lastEdit, string $taskText = "", string $userIdOfLastEdit = "")
    {
        $this->todoId = $taskId;
        $this->createdOn = new DateTime();
        $this->userId = $userId;
        $this->todoTitle = $taskTitle;
        $this->todoText = $taskText;
        $this->lastEdit = $lastEdit;
        $this->userIdOfLastEdit = $userIdOfLastEdit;
        $this->status = $taskStatus;
    }

    /**
     * @param string $name the name of the required attribute
     * @return mixed returns the required attribute, if it exists
     * @throws Exception throws exception, when attribute doesn't exist
     */
    public function __get(string $name): mixed
    {
        if (property_exists("TodolistItem", $name))
        {
            return $this->{$name};
        }
        else
        {
            throw new Exception("Attribute " . $name . " does not exist in class TodoListItem!");
        }
    }

    /**
     * @param string $name the attribute which value should be changed
     * @param string $value the new value for the attribute
     * @throws Exception throws exception, when attribute doesn't exist
     */
    public function __set(string $name, string $value): void
    {
        if (property_exists("TodolistItem", $name))
        {
            $this->{$name} = $value;
        }
        else
        {
            throw new Exception("Attribute " . $name . " does not exist in class TodoListItem!");
        }
    }

    /**
     * This function returns a formatted todoListItem
     * @return string formatted todoListItem
     */
    public function __toString(): string
    {
        $PHPSELF = $_SERVER["PHP_SELF"];
        $result = "
        <div class='" . $this->todoId . " " . $this->userId . " " . $this->status . "'>
        <div class='w-full h-15 pl-3 pr-3 py-4 bg-gray-200 rounded-lg focus:shadow-outline col-start-1 mb-2'>
            <div class='grid grid-row-1 grid-cols-12'>";

        //select style based on status of todoListItem
        if ($this->status)
        {

            $result .= "
            <form action='$PHPSELF?id=$this->todoId' method='post'>
                <button type='submit' class='text-indigo-600 col-start-1 justify-self-center self-center'>
                <svg xmlns='http://www.w3.org/2000/svg' width='32' height='32' fill='currentColor' class='bi bi-check-circle' viewBox='0 0 16 16'>
                    <path d='M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z'/>
                    <path d='M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z'/>
                </svg>
                </button>
                <input type='hidden' name='action' value='changeStatus'>
            </form>
            <p class='flex text-black font-bold items-center m-0 px-2 col-start-2 col-end-10'>" . $this->todoTitle . "</p>";
        }
        else
        {
            $result .= "
            <form action='$PHPSELF?id=$this->todoId' method='post'>
                <button type='submit' class='text-indigo-600 col-start-1 justify-self-center self-center'>
                    <svg xmlns='http://www.w3.org/2000/svg' width='32' height='32' fill='currentColor' class='bi bi-check-circle-fill' viewBox='0 0 16 16'>
                        <path d='M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z'/>
                    </svg>
                </button>
                <input type='hidden' name='action' value='changeStatus'>
            </form>
               <p class='line-through flex text-black font-bold items-center m-0 px-2 col-start-2 col-end-10'>" . $this->todoTitle . "</p>";
        }
        $result .= "
        <form action='$PHPSELF?id=$this->todoId&title=$this->todoTitle&text=$this->todoText' method='post'>
            <button type='submit' class='col-start-11'>
                <svg xmlns='http://www.w3.org/2000/svg'
                     class='place-items-center w-8 text-indigo-700 fill-current cursor-pointer'
                     viewBox='0 0 20 20' fill='currentColor'>
                    <path d='M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z'/>
                    <path fill-rule='evenodd'
                          d='M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z'
                          clip-rule='evenodd'/>
                </svg>
            </button>
            <input type='hidden' name='action' value='editTodo'>
            </form>
        <form action='$PHPSELF?id=$this->todoId' method='post'>
            <button type='submit' class='col-start-12'>
                <svg xmlns='http://www.w3.org/2000/svg'
                     class='place-items-center w-8 text-indigo-700 fill-current cursor-pointer'
                     viewBox='0 0 20 20' fill='currentColor'>
                    <path fill-rule='evenodd'
                          d='M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z'
                          clip-rule='evenodd'/>
                </svg>
            </button>
            <input type='hidden' name='action' value='deleteTodo'>
        </form>";

        //don't print description if there is none
        if ($this->todoText != "")
        {
            if ($this->status)
            {
                $result .= " <p class='flex text-black text-sm font-normal items-center m-0 pb-2 px-2 col-start-2 col-end-12' > $this->todoText</p> ";
            }
            else
            {
                $result .= " <p class='line-through flex text-black text-sm font-normal items-center m-0 pb-2 px-2 col-start-2 col-end-12' > $this->todoText</p> ";
            }

        }

        //dont' print info on lastEdit, if there was no modification (no modification if date is 01/01/1900)
        if ($this->lastEdit != new DateTime('01/01/1900'))
        {
            $insertText = "last edited by $this->userIdOfLastEdit on " . $this->lastEdit->format("d.m.Y @ H:i");
            $result .= "<p class='flex text-black font-light text-xs items-center m-0 px-2 col-start-2 col-end-12 italic'>$insertText</p> ";
        };
        $result .= '</div> </div></div>';
        return $result;
    }
}