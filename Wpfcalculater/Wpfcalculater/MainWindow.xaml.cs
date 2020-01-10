using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Navigation;
using System.Windows.Shapes;
using MySql.Data.MySqlClient;
using System.Data;

namespace Wpfcalculater
{
    /// <summary>
    /// MainWindow.xaml 的互動邏輯
    /// </summary>
    public partial class MainWindow : Window
    {

        



        public MainWindow()
        {
            InitializeComponent();
        }

        private void Sevenb_Click(object sender, RoutedEventArgs e)
        {
            ZeroRemove(7);
        }

        private void ZeroRemove(int number)

        {

            if (moniter.Text == "0")

                moniter.Text = number.ToString();

            else

                moniter.Text += number.ToString();

        }



        private void Zerob_Click(object sender, RoutedEventArgs e)
        {
            ZeroRemove(0);
        }

        private void Oneb_Click(object sender, RoutedEventArgs e)
        {
            ZeroRemove(1);
        }

        private void Twob_Click(object sender, RoutedEventArgs e)
        {
            ZeroRemove(2);
        }

        private void Threeb_Click(object sender, RoutedEventArgs e)
        {
            ZeroRemove(3);
        }

        private void Fourb_Click(object sender, RoutedEventArgs e)
        {
            ZeroRemove(4);
        }

        private void Fiveb_Click(object sender, RoutedEventArgs e)
        {
            ZeroRemove(5);
        }

        private void Sixb_Click(object sender, RoutedEventArgs e)
        {
            ZeroRemove(6);
        }

        private void Eightb_Click(object sender, RoutedEventArgs e)
        {
            ZeroRemove(8);
        }

        private void Nineb_Click(object sender, RoutedEventArgs e)
        {
            ZeroRemove(9);
        }

        private void Divideb_Click(object sender, RoutedEventArgs e)
        {
            moniter.Text += '/'.ToString();
        }

        private void Multiplyb_Click(object sender, RoutedEventArgs e)
        {
            moniter.Text += '*'.ToString();
        }

        private void Minusb_Click(object sender, RoutedEventArgs e)
        {
            moniter.Text += '-'.ToString();
        }

        private void Plusb_Click(object sender, RoutedEventArgs e)
        {
            moniter.Text += '+'.ToString();
        }

        private void Enterb_Click(object sender, RoutedEventArgs e)
        {
            string infix = moniter.Text;
            string result = string.Empty;

            // initializing empty stack
            Stack<char> stack = new Stack<char>();

            for (int i = 0; i < moniter.Text.Length; ++i)
            {
                char c = moniter.Text[i];

                // If the scanned character is an operand, add it to output.
                if (Char.IsLetterOrDigit(c))
                    result += c;

                // If the scanned character is an '(', push it to the stack.
                else if (c == '(')
                    stack.Push(c);

                //  If the scanned character is an ')', pop and output from the stack 
                // until an '(' is encountered.
                else if (c == ')')
                {
                    while (stack.Count != 0 && stack.Peek() != '(')
                    {
                        result += stack.Pop();


                        stack.Pop();
                    }
                        
                }
                else // an operator is encountered
                {
                    while (stack.Count != 0 && Prec(c) <= Prec(stack.Peek()))
                        result += stack.Pop();
                    stack.Push(c);
                }

            }

            // pop all the operators from the stack
            while (stack.Count != 0)
                result += stack.Pop();
            posfix.Text = result;
            prefix.Text = PostToPre(result);
            moniter.Text = "0";

            Stack<int> num = new Stack<int>();
            int op1, op2;

            for (int i = 0; i < posfix.Text.Length; ++i)
            {


                int resul = Convert.ToInt32(posfix.Text[i]);

                if (Char.IsLetterOrDigit(posfix.Text[i]))
                    num.Push(resul - '0');
                else
                {
                    op2 = num.Pop();
                    op1 = num.Pop();
                    num.Push(Cal_exp(op1, op2, posfix.Text[i]));
                }
            }
            int resultofans = num.Pop();
            string varString = resultofans.ToString();

            decima.Text = varString;

            
            binary.Text= Convert.ToString(resultofans, 2);
            
            

            
        }

        private void Acb_Click(object sender, RoutedEventArgs e)
        {
            moniter.Clear();

            moniter.Text = "0";
        }

        private void Moniter_TextChanged(object sender, TextChangedEventArgs e)
        {
            
        }

        private void Prefix_TextChanged(object sender, TextChangedEventArgs e)
        {

        }

        private void Posfixlabel_TextChanged(object sender, TextChangedEventArgs e)
        {

        }

        private void Decima_TextChanged(object sender, TextChangedEventArgs e)
        {

        }

        private void Binary_TextChanged(object sender, TextChangedEventArgs e)
        {

        }

        private void Perfix_TextChanged(object sender, TextChangedEventArgs e)
        {

        }

        private void Posfix_TextChanged(object sender, TextChangedEventArgs e)
        {

        }

        private void Perfixlabel_TextChanged(object sender, TextChangedEventArgs e)
        {

        }

        private void Decimallabel_TextChanged(object sender, TextChangedEventArgs e)
        {

        }

        private void Binarylabel_TextChanged(object sender, TextChangedEventArgs e)
        {

        }

        public static int Prec(char ch)
        {
            switch (ch)
            {
                case '+':
                case '-':
                    return 1;

                case '*':
                case '/':
                    return 2;

                case '^':
                    return 3;
            }
            return -1;
        }
        public static  int Cal_exp(int op1, int op2, char op)
        {
            if (op == '+') return op1 + op2;
            
            if (op == '-') return op1 - op2;

            if (op == '*') return op1 * op2;
            if (op == '/') return op1 / op2;
            else return -1;
        }
        public static Boolean IsOperator(char x)
        {

            switch (x)
            {
                case '+':
                case '-':
                case '/':
                case '*':
                    return true;
            }
            return false;
        }

        // Convert postfix to Prefix expression 
        public static String PostToPre(String post_exp)
        {
            string result = string.Empty;
            Stack<string> s = new  Stack<string>();

            // length of expression 
            int length = post_exp.Length;
            
            // reading from right to left 
            for (int i = 0; i < length; i++)
            {
                int resul = Convert.ToInt32(post_exp[i]);
                // check if symbol is operator 
                if (IsOperator(post_exp[i]))
                {

                    // Pop two operands from stack 
                    string op1 = s.Peek();
                    s.Pop();
                    
                    string op2 = s.Peek();
                    s.Pop();

                    // concat the operands and operator 
                    string temp = post_exp[i]+op2 +op1;
                    
                    
                    // Push String temp back to stack 
                    s.Push(temp);
                    
                }

                // if symbol is an operand 
                else
                {

                    // Push the operand to the stack 
                    s.Push(post_exp[i]+"");
                }
            }
            
            // stack[0] contains the Prefix expression 
            return s.Peek();
        }

        private void Insert_Click(object sender, RoutedEventArgs e)
        {
            string connString = "server=127.0.0.1;port=3306;user id=root;password=sheisyuri0405;database=cal;Allow User Variables=True;";
            MySqlConnection conn = new MySqlConnection();
            conn.ConnectionString = connString;
            if (conn.State != ConnectionState.Open)
                conn.Open();
            string sql = "INSERT INTO cal (prefi, postfi,decima,binar) VALUES ('"+prefix.Text+ "', '" + posfix.Text + "','" + decima.Text + "','" + binary.Text + "');";

            MySqlCommand cmd = new MySqlCommand(sql, conn);
            int index = cmd.ExecuteNonQuery();
            bool success = false;
            if (index > 0)
                success = true;
            else
                success = false;
            conn.Close();
        }

        private void See_Click(object sender, RoutedEventArgs e)
        {
            Window1 window = new Window1();  
            window.Show();   
            
        }
    }
}
